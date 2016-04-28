<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;

/**
 * Class UTopolu
 *
 * Parser for http://www.utopolu.cz/
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
class UTopolu extends AbstractParser
{

    protected $filter = [
        "^&nbsp;$",
        "^[\xa0\xc2[:space:]]*$",
        "Z[\xa0\xc2[:space:]]*našeho[\xa0\xc2[:space:]]*menu",
        "U[\xa0\xc2[:space:]]*Topolů",
        "Svátek",
    ];

    protected static $selector = 'table.content tr';

    public function isSupported($format)
    {
        return ($format == 'utopolu');
    }

    public function supports()
    {
        return [ 'utopolu' ];
    }

    public function parse($format, $data, $charset = 'UTF-8')
    {
        $data = json_decode($data);
        if (!empty($data->menu))
            return parent::parse($format, $data->menu, $charset);
        else
            return [ 'Menu není k dispozici' ];
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
            if (isset($row[0]) && (!isset($row[1]) || !isset($row[2])) && preg_match("/{$skip}/", $row[0]))
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
        $result = [];
        $key = null;
        foreach ($data as $row) {
            if (!is_array($row) || $this->filter($row))
                continue;
            if (count($row) == 1) {
                if (preg_match('/Polévky/', $row[0]))
                    $key = static::KEY_SOUPS;
                else if (preg_match('/Hotová.jídla/', $row[0]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Bezmasá jídla/', $row[0]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Doporučujeme/', $row[0]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Menu/', $row[0]))
                    $key = static::KEY_MENU;
                else if (preg_match('/Saláty/', $row[0]))
                    $key = static::KEY_SALADS;
                else if (preg_match('/Dezert/', $row[0]))
                    $key = static::KEY_DESERTS;
                else
                    $key = null;
                continue;
            }
            if ($key !== null && !empty($row[1])) {
                $result[$key][] = [
                    trim($row[1]),
                    (!empty($row[2]) ? 0 + $row[2] : '-')
                ];
            }
        }
        return $result;
    }

}