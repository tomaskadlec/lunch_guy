<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service;

/**
 * Interface ParserInterface
 *
 * Common interface of all parsers
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service
 */
interface ParserInterface
{

    const KEY_SOUPS = 'Polévky';
    const KEY_MAIN = 'Hlavní jídla';
    const KEY_SALADS = 'Saláty';
    const KEY_MENU = 'Menu';
    const KEY_DESERTS = 'Dezerty';

    /**
     * Returns true if format is supported by the parser
     *
     * @param string $format
     * @return bool
     */
    public function isSupported($format);

    /**
     * Returns array of formats the parser supports, e.g [ 'drest' ].
     *
     * @return array
     */
    public function supports();

    /**
     * Parse menu from a restaurant (typically HTML)
     *
     * @param string $format format of data
     * @param string $data data to parse
     * @param string $charset charset of the data
     * @return array of parsed meals
     */
    public function parse($format, $data, $charset = 'UTF-8');

}