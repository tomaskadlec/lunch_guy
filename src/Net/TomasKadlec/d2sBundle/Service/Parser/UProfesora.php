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

    protected static $selector = 'div#about div.tab-content div#sectionA table.menulist tr';

    protected static $selectorDate = 'div#about div.tab-content div#sectionA table.menulist';

    public function isSupported($format)
    {
        return ($format == 'uprofesora');
    }

    public function supports()
    {
        return [ 'uprofesora' ];
    }


    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");
        $date = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selectorDate)
            ->first()
            ->attr('id');
        $today = true;
        if (!empty($date) && is_string($date)) {
            $date = preg_replace('/^[^.0-9[:space:]]+[[:space:]]+/', '', $date);
            $date = \DateTime::createFromFormat('j.n.Y', $date);
            if ($date !== false && ((new \DateTime('today'))->getTimestamp() - $date->getTimestamp()) > (24 * 60 * 60))
                $today = false;
        }

        if ($today) {
            return parent::parse($format, $data, $charset);
        } else {
            return [];
        }
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