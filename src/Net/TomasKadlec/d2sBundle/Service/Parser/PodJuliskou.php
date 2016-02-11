<?php
namespace Net\TomasKadlec\d2sBundle\Service\Parser;

/**
 * Class PodJuliskou
 *
 * Parser implementation for http://www.podjuliskou.cz/menu/
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Parser
 */
class PodJuliskou extends AbstractParser
{
    protected $filter = [
    ];

    protected static $selector = 'h2#poledni ~ h3 + ul';

    protected static $selectorDate = 'h2#poledni ~ h3';

    public function isSupported($format)
    {
        return ($format == 'podjuliskou');
    }

    public function supports()
    {
        return [ 'podjuliskou' ];
    }

    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");

        $menu = [];
        if ($this->isForToday($data, $charset)) {
            /** @var \DOMElement $node */
            $i = 0;
            foreach ($this->getCrawler($data, $charset)->filter(static::$selector)->children() as $node) {
                $i++;
                $result = [
                    $node->firstChild->nodeValue,
                    preg_replace('/\s*Kč/', '', $node->lastChild->nodeValue),
                ];
                $key = static::KEY_MAIN;
                if ($i == 1) {
                    $key = static::KEY_SOUPS;
                }
                $menu[$key][] = $result;
            }
        }
        return $menu;
    }

    /**
     * Checks if menu is for today
     * @param string $data
     * @param string $charset
     * @return bool true if it is for today else otherwise
     */
    protected function isForToday($data, $charset)
    {
        $i = 0;
        /** @var \DOMElement $node */
        foreach ($this->getCrawler($data, $charset)->filter(static::$selectorDate) as $node) {
            $i++;
            if ($i == 2) {
                preg_match('/[0-9]{1,2}.\s*[0-9]{1,2}.\s*[0-9]{4,4}/', $node->nodeValue, $matches);
                if (isset($matches[0]))
                    $date = $matches[0];
                    continue;
            }
        }

        if (!empty($date) && is_string($date)) {
            $date = preg_replace('/[[:space:]]+/', '', $date);
            $date = \DateTime::createFromFormat('j.n.Y', $date);
            if ($date !== false) {
                $today = new \DateTime('today');
                $tomorrow = new \DateTime('tomorrow');
                if ($date < $today || $date >= $tomorrow)
                    return false;
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function process($data)
    {
        return $data;
    }

}