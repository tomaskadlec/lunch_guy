<?php
namespace Net\TomasKadlec\d2sBundle\Service;

/**
 * Interface OutputInterface
 *
 * Common interface to handle application output
 *
 * @package Net\TomasKadlec\d2sBundle\Output
 */
interface OutputInterface
{
    /**
     * Returns true if format is supported by the output
     *
     * @param string $format
     * @return bool
     */
    public function isSupported($format);

    /**
     * Returns array of formats the output supports, e.g [ 'slack' ].
     *
     * @return array
     */
    public function supports();

    /**
     * Formats data according to the output service needs
     *
     * @param string $format
     * @param string $restaurantId
     * @param array $menu structured data to be formated
     * @return string
     */
    public function format($format, $restaurantId, $menu);

    /**
     * Sends formatted data to the output service
     *
     * @param string $format
     * @param string $restaurantId
     * @param string $menu formatted data to send
     * @param array $options
     */
    public function send($format, $restaurantId, $menu, $options = []);
}