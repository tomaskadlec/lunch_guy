<?php
namespace Net\TomasKadlec\d2sBundle\Service\Parser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SimiGastro
 *
 * Parser implementation for SimiGastro (Cesta Casem)
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Parser
 */
class SimiGastro extends AbstractParser
{

    protected static $selector = 'div#jidelnicek > *';

    public function isSupported($format)
    {
        return ($format == 'simigastro');
    }

    public function supports()
    {
        return [ 'simigastro' ];
    }

    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");
        $data = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selector)
            ->each(function (Crawler $node) {
                if ($node->nodeName() == 'h3')
                    return ['', $node->text()];
                else if ($node->nodeName() == 'dl')
                    return $node->children()->each(function(Crawler $child) {
                        return $child->text();
                    });
            });
        return $this->process($data);
    }

    /**
     * Transforms data from the crawler to an internal array
     *
     * @param $data
     * @return array
     */
    protected function process($data)
    {
        $key = null;
        $result = [];

        foreach ($data as $row) {
            if (empty($row[0])) {
                if (preg_match('/Polévka/', $row[1]))
                    $key = static::KEY_SOUPS;
                else if (preg_match('/Šéfkuchař doporučuje/', $row[1]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Hotovky/', $row[1]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Salát/', $row[1]))
                    $key = static::KEY_SALADS;
                else if (preg_match('/^Menu/', $row[1]))
                    $key = static::KEY_MENU;
                else
                    $target = null;
                //continue;
            }
            else if ($key !== null) {
                $result[$key][] = [
                    preg_replace("/^([0-9][0-9a-zA-Z]*)*[ \xa0\xc2]+/", '', trim($row[0])),
                    (!empty($row[1]) ? 0 + $row[1] : '-')
                ];
            }
        }
        return $result;
    }
}