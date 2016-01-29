<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 28.1.16
 * Time: 7:20
 */

namespace Net\TomasKadlec\d2sBundle\Service;

/**
 * Interface ApplicationInterface
 * @package Net\TomasKadlec\d2sBundle\Service
 */
interface ApplicationInterface
{

    /**
     * Returns IDs of all restaurants configured
     *
     * @return array
     */
    public function getRestaurants();

    /**
     * Returns true if restaurant with given ID exists
     *
     * @param $restaurantId
     * @return bool
     */
    public function isRestaurant($restaurantId);

    /**
     * Returns IDs of all registered parsers
     * @return array
     */
    public function getParsers();

    /**
     * Returns true if parser with given ID is registered
     * @param string $parser ID
     * @return bool
     */
    public function isParser($parser);

    /**
     * Returns IDs of all configured outputs
     *
     * @return array
     */
    public function getOutputs();

    /**
     * Returns true if output with given ID exists
     *
     * @param string $output ID
     * @return bool
     */
    public function isOutput($output);

    /**
     * Retrieves menu of selected restaurant and returns it parsed
     * into an inner form.
     *
     * @param $restaurantId
     * @return array
     */
    public function retrieve($restaurantId);

    /**
     * Outputs menu of restaurantId
     *
     * @param string $restaurantId
     * @param string $output
     * @param array $options options to override ones read from configuration
     */
    public function output($restaurantId, $output, array $options = []);

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration);

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output);

    /**
     * @param ParserInterface $parser
     */
    public function setParser($parser);

}