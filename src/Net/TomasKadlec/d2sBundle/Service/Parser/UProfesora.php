<?php
namespace Net\TomasKadlec\d2sBundle\Service\Parser;

/**
 * Class DRest
 *
 * Parser implementation for http://uprofesora.cz/
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Parser
 */
class UProfesora extends AbstractParser
{
    protected $filter = [
    ];

    protected static $selector = 'div#about div.tab-content div#sectionA table tr';

    public function isSupported($format)
    {
        return ($format == 'uprofesora');
    }

    public function supports()
    {
        return [ 'uprofesora' ];
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
        $soup = true;
        foreach ($data as $row) {
            if ($soup) {
                $key = static::KEY_SOUPS;
                $soup = false;
            } else {
                $key = static::KEY_MAIN;
            }

            if ($key !== null && !empty($row[0])) {
                $result[$key][] = [
                    trim(preg_replace('/[(]obsahuje[^)]*[)]+/', '', $row[0])),
                    (!empty($row[1]) ? 0 + $row[1] : '-')
                ];
            }
        }
        return $result;
    }
}