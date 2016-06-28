<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Budvarka
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
class Budvarka extends AbstractParser
{

    /** @inheritdoc */
    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not upported.");

        // no menu on Saturdays and Sundays
        $day = date('N');
        if ($day == 6 || $day == 7)
            return [];

        $result = [];
        $key = null;

        $this
            ->getCrawler($data, $charset)
            ->filter('table.foodMenu.detail tr')
            ->each(function(Crawler $crawler) use (&$result, &$key) {
                try {
                    $meal = $crawler->filter('td:nth-child(3)')->text();
                    $meal = preg_replace('/[[:space:]]*Alergeny:.*$/', '', $meal);
                    $price = 0 + $crawler->filter('td:nth-child(4)')->text();

                    $key = static::KEY_MAIN;
                    if (preg_match('/polévk/i', $meal)) {
                        $key = static::KEY_SOUPS;
                    }
                    $result[$key][] = [$meal, $price];
                } catch (\Exception $e) {
                }
            })
        ;
        return $result;
    }

    /** @inheritdoc */
    protected function process($data)
    {
        throw new \RuntimeException("Method is not implemented.");
    }

    /** @inheritdoc */
    public function isSupported($format)
    {
        return ($format == 'budvarka');
    }

    /** @inheritdoc */
    public function supports()
    {
        return ['budvarka'];
    }

}