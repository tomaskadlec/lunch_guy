<?php
namespace Net\TomasKadlec\d2sBundle\Service\Parser;

/**
 * Class SUZ
 *
 * Parser implementation for http://agata.suz.cvut.cz/jidelnicky
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Parser
 */
class SUZ extends AbstractParser
{

    protected $filter = [];

    protected static $selector = 'div#jidelnicek table tbody tr';

    public function isSupported($format)
    {
        return ($format == 'suz');
    }

    public function supports()
    {
        return [ 'suz' ];
    }

    public function parse($format, $data, $charset = 'UTF-8')
    {
        $data = preg_replace('/&nbsp;/', ' ', $data);
        return parent::parse($format, $data, $charset);
    }

    /**
     * Transforms data from the crawler to an internal array
     *
     * @param $data
     * @return array
     */
    protected function process($data) {
        $key = null;
        $result = [];
        foreach ($data as $row) {
            if (empty($row))
                continue;
            if (count($row) == 1) {
                if (preg_match('/Polévky/', $row[0]))
                    $key = 'Polévky';
                else if (preg_match('/(Specialita dne)|(Hlavní jídla)|(Minutky)/', $row[0]))
                    $key = 'Hlavní jídla';
                else if (preg_match('/(Bezmasá jídla)|(Vegetariánská jídla)/', $row[0]))
                    $key = 'Hlavní jídla';
                else if (preg_match('/Moučníky/', $row[0]))
                    $key = 'Dezerty';
                else if (preg_match('/Menu/', $row[0]))
                    $key = 'Menu';
                else
                    $target = null;
                continue;
            }
            if ($key !== null) {
                $tmp = [
                    trim($row[1]),
                    0 + trim($row[5]),
                ];
                if (isset($row[6])) {
                    $value = trim(preg_replace('/[^J0-9]+/', ' ', $row[6]));
                    if (!empty($value))
                        $tmp[] = $value;
                }
                $result[$key][] = $tmp;
            }
        }
        return $result;
    }

}