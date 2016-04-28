<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PodLoubim
 *
 * Parser implementation for http://www.podloubim.com/menu/
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
class PodLoubim extends AbstractParser
{
    protected $remove = [

    ];

    protected static $selector = 'div.box table tr';

    public function isSupported($format)
    {
        return ($format == 'podloubim');
    }

    public function supports()
    {
        return [ 'podloubim' ];
    }


    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");
        $data = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selector)
            ->each(function (Crawler $node) {
                $array = [];
                /** @var \DOMElement $child */
                foreach($node->children() as $child) {
                    $array[] = trim(preg_replace('/(&nbsp;|\s|[\xa0\xc2])+/', ' ', mb_strtolower($child->nodeValue, 'UTF-8')));
                }
                return $array;
            });
        return $this->process($data);
    }

    /**
     * Takes decision on filtering data resulting from the crawler
     *
     * @param $row
     * @return bool
     */
    protected function filter($row)
    {
        if (empty($row))
            return true;
        foreach ($this->filter as $skip) {
            if (isset($row[1]) && preg_match("/{$skip}/", $row[1]))
                return true;
        }
        return false;
    }

    /**
     * Transforms data from the crawler to an internal array
     *
     * @param $data
     * @return array
     */
    protected function process($data)
    {
        $day = null;
        $key = null;
        $soup = true;
        $result = [];

        /** @var \DOMNode $node */
        foreach ($data as $row) {
            if (!empty($row[1]) &&
                preg_match('/(pondělí|úterý|středa|čtvrtek|pátek|sobota|neděle)/i', $row[1], $matches)) {
                $day = $this->getDayOfWeek($matches[0]);
                if ($day != date('w')) $day = '';
            } else if (!empty($day)) {
                if ($soup) {
                    $key = static::KEY_SOUPS;
                    $soup = false;
                } else {
                    $key = static::KEY_MAIN;
                }

                if ($key !== null && !empty($row[1])) {
                    $result[$key][] = [
                        ucfirst(mb_strtolower($row[1], 'UTF-8')),
                        (!empty($row[0]) ? 0 + $row[0] : '-')
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * Return day number in week
     *
     * @param $day
     * @return bool|int
     */
    protected function getDayOfWeek($day)
    {
        switch (strtolower(trim($day))) {
            case 'pondělí':
                return 1;
            case 'úterý':
                return 2;
            case 'středa':
                return 3;
            case 'čtvrtek':
                return 4;
            case 'pátek':
                return 5;
            case 'sobota':
                return 6;
            case 'neděle':
                return 0;
            default:
                return false;
        }
    }
}