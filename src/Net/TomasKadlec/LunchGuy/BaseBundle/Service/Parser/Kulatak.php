<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Kulatak
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
class Kulatak extends AbstractParser
{

    const FILTERS = [
        '^[[:space:]]+',
        '[[:space:]]+$',
    ];

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
            ->filter('div.subBlock[value="1"] > div:nth-child('. $day. ')')
            ->filter('div.html > div.cont')
            ->children()
            ->each(function(Crawler $crawler) use (&$result, &$key) {
                try {
                    if ($crawler->nodeName() == 'h4') {
                        $key = static::KEY_MAIN;
                        if (preg_match('/Polévk/', $crawler->text())) {
                            $key = static::KEY_SOUPS;
                        }
                    } else {
                        if ($key == null) {
                            return;
                        }

                        $meal = $crawler->filter('td > div.nadpis.first')->first()->text();
                        foreach (static::FILTERS as $pattern) {
                            $meal = mb_ereg_replace($pattern, '', $meal);
                        }

                        $result[$key][] = [
                            $meal,
                            $price = 0 + $crawler->filter('td > div.prize:nth-child(2)')->first()->text(),
                        ];
                    }
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
        return ($format == 'kulatak');
    }

    /** @inheritdoc */
    public function supports()
    {
        return ['kulatak'];
    }

}