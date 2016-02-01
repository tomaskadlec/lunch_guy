<?php
namespace Net\TomasKadlec\d2sBundle\Service\Parser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Santinka
 *
 * Parser implementation for http://www.santinka.cz/
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Parser
 */
class Santinka extends AbstractParser
{
    protected $filter = [
        'Denní menu'
    ];

    protected static $selector = 'div.daily-menu';

    protected static $selectorDate = 'div.daily-menu h2';

    /**
     * @inheritdoc
     */
    public function isSupported($format)
    {
        return ($format == 'santinka');
    }

    /**
     * @inheritdoc
     */
    public function supports()
    {
        return [ 'santinka' ];
    }

    /**
     * @inheritdoc
     */
    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");
        $date = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selectorDate)
            ->first()
            ->text();
        $today = true;
        if (!empty($date) && is_string($date)) {
            $date = preg_replace('/^[^0-9]+/', '', $date);
            $date = $this->parseDate($date);
            if ($date !== false && ((new \DateTime('today'))->getTimestamp() - $date) > (24 * 60 * 60))
                $today = false;
        }

        if (!$today)
            return [];

        $data = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selector)
            ->first()
            ->text();

        $lines = explode("\n", $data);
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;
            $data[] = explode('-', $line);
        }
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
        $key = null;
        $result = [];
        foreach ($data as $row) {
            if (!isset($row[1])) {
                if ($row[0] == 'Polévka') {
                    $key = static::KEY_SOUPS;
                } else if ($row[0] == 'Hlavní jídlo') {
                    $key = static::KEY_MAIN;
                }
                continue;
            }

            if ($key !== null && !empty($row[0])) {
                $result[$key][] = [
                    $this->mb_ucfirst(mb_strtolower(trim($row[0]), 'UTF-8')),
                    (!empty($row[1]) ? 0 + trim($row[1]) : '-')
                ];
            }
        }
        return $result;
    }

    /**
     * Parses czech date format into a timestamp
     *
     * @param string $date date in czech formatted 'day. monthName year'
     * @return number|bool timestamp or FALSE in case of failure
     */
    protected function parseDate($date) {
        $date = str_replace('.', '', $date);
        $date = preg_replace('/[[:space:]]+/', ' ', $date);
        list($day, $month, $year) = explode(' ', $date);
        switch ($month) {
            case 'leden':
            case 'ledna':
                $month = 1; break;
            case 'únor':
            case 'února':
                $month = 2; break;
            case 'březen':
            case 'března':
                $month = 3; break;
            case 'duben':
            case 'dubna':
                $month = 4; break;
            case 'květen':
            case 'května':
                $month = 5; break;
            case 'červen':
            case 'června':
                $month = 6; break;
            case 'červenec':
            case 'července':
                $month = 7; break;
            case 'srpen':
            case 'srpna':
                $month = 8; break;
            case 'září':
                $month = 9; break;
            case 'říjen':
            case 'října':
                $month = 10; break;
            case 'listopad':
            case 'listopadu':
                $month = 11; break;
            case 'prosinec':
            case 'prosince':
                $month = 12; break;
        }
        $date = \DateTime::createFromFormat('j.n.Y', "$day.$month.$year");
        if ($date)
            return $date->getTimestamp();
        else
            return false;
    }

    protected function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)).(mb_substr($string, 1));
    }
}