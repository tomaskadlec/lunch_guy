<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Kulatak
 *
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
        if (!$this->isSupported($format)) {
            return new \RuntimeException("Format {$format} is not upported.");
        }

        // no menu on Saturdays and Sundays
        $day = date('N') - 1;
        if ($day == 5 || $day == 6) {
            return [];
        }

        $result = [];
        $key = null;

        $this
            ->getCrawler($data, $charset)
            ->filter(
                'div#services-section2 > div.container > div#weekly-menu-wrapper > div#weekly-menu > div#daily_menu > table'
            )
            ->slice($day, 1)
            ->filter('tbody > tr')
            ->slice(1)
            ->each(
                function (Crawler $crawler) use (&$result, &$key) {
                    try {
                        $tds = $crawler->children();

                        if (preg_match('/Polévk/', $tds->getNode(0)->textContent)) {
                            $key = static::KEY_SOUPS;
                        } elseif (preg_match('/Jídlo/', $tds->getNode(0)->textContent)) {
                            $key = static::KEY_MAIN;
                        }

                        if (is_null($key)) {
                            return;
                        }

                        $meal = $this->getTableCellContent($tds, 1);
                        $price = $this->getTableCellContent($tds, 2, true);

                        $result[$key][] = [
                            $meal,
                            $price,
                        ];
                    } catch (\Exception $e) {
                    }
                }
            );
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

    /**
     * @param Crawler $crawler
     * @param int     $cellPosition
     * @param bool    $integer
     * @return int|string
     */
    public function getTableCellContent(Crawler $crawler, $cellPosition, $integer = false)
    {
        $content = $crawler->getNode($cellPosition)->textContent;
        foreach (static::FILTERS as $pattern) {
            $content = mb_ereg_replace($pattern, '', $content);
        }

        if ($integer) {
            return intval($content);
        }

        return $content;
    }

}