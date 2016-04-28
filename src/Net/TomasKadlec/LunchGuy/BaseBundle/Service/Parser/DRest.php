<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;

/**
 * Class DRest
 *
 * Parser implementation for drest.cz
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
class DRest extends AbstractParser
{
    protected $filter = [
        'Denní menu',
        'Každý den',
        'Poloviční porce',
        'Denní nabídka',
        'Na stravenky',
        'Děkujeme Vám',
        'Navštivte naše',
        'drest.cz',
        'Nové stránky',
    ];

    protected static $selector = 'table.es_resto_menu tr';

    public function isSupported($format)
    {
        return ($format == 'drest');
    }

    public function supports()
    {
        return [ 'drest' ];
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
        $key = null;
        $result = [];
        foreach ($data as $row) {
            if ($this->filter($row))
                continue;
            if (count($row) == 1) {
                if (preg_match('/Polévky/', $row[0]))
                    $key = static::KEY_SOUPS;
                else if (preg_match('/Hlavní jídla/', $row[0]))
                    $key = static::KEY_MAIN;
                else if (preg_match('/Denní menu/', $row[0]))
                    $key = static::KEY_MENU;
                else
                    $target = null;
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