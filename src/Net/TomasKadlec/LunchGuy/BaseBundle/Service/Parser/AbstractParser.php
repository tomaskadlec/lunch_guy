<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser;

use Net\TomasKadlec\LunchGuy\BaseBundle\Service\ParserInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AbstractParser
 *
 * Abstract class providing some useful common methods
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Parser
 */
abstract class AbstractParser implements ParserInterface
{
    protected static $selector = 'table tr';

    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format {$format} is not supported.");
        $data = $this
            ->getCrawler($data, $charset)
            ->filter(static::$selector)
            ->each(function (Crawler $node) {
                return $node->children()->each(function(Crawler $child) {
                    return $child->text();
                });
            });
        return $this->process($data);
    }

    /**
     * Creates an instance of the Crawler and adds content in it
     *
     * @param string $data HTML fragment to parse
     * @param string $charset used charset, defaults to UTF-8
     * @return Crawler
     */
    protected function getCrawler($data, $charset = 'UTF-8')
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($data, $charset);
        return $crawler;
    }

    /**
     * Transforms data from the crawler to an internal array
     *
     * @param $data
     * @return array
     */
    protected abstract function process($data);

}