<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service;

/**
 * Class Parser
 *
 * A front object acting as a registry of available parses as well as strategy
 * for taking decisions which one to use.
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service
 */
class Parser implements ParserInterface
{
    /**
     * @var ParserInterface[]
     */
    protected $parsers;

    public function __construct()
    {
        $this->parsers = [];
    }

    /**
     * Adds a new parser to be used
     *
     * @param ParserInterface $parser
     */
    public function add(ParserInterface $parser) {
        foreach ($parser->supports() as $support) {
            $this->parsers[$support] = $parser;
        }
    }

    public function isSupported($format)
    {
        foreach ($this->parsers as $parser) {
            if ($parser->isSupported($format))
                return true;
        }
        return false;
    }

    public function supports()
    {
        $supports = [];
        foreach ($this->parsers as $parser) {
            $supports = array_merge($supports, $parser->supports());
        }
        return $supports;
    }

    public function parse($format, $data, $charset = 'UTF-8')
    {
        if (!$this->isSupported($format))
            return new \RuntimeException("Format $format is not supported.");
        return $this->parsers[$format]->parse($format, $data, $charset);
    }

}