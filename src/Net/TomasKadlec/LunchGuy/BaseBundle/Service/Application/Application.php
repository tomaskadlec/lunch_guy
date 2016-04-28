<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 28.1.16
 * Time: 7:11
 */

namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Application;
use GuzzleHttp\Client;
use Net\TomasKadlec\LunchGuy\BaseBundle\Service\ApplicationInterface;
use Net\TomasKadlec\LunchGuy\BaseBundle\Service\OutputInterface;
use Net\TomasKadlec\LunchGuy\BaseBundle\Service\ParserInterface;

/**
 * Class Application
 *
 * Non-caching implementation of the application
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service
 */
class Application implements ApplicationInterface
{
    /**
     * Configuration (parameters)
     * @var array
     */
    protected $configuration;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @inheritdoc
     */
    public function getRestaurants()
    {
        $restaurants = $this->configRestaurants();
        if (is_array($restaurants))
            return array_keys($restaurants);
        else
            return [];
    }

    /**
     * @inheritdoc
     */
    public function isRestaurant($restaurantId)
    {
        try {
            $this->configRestaurant($restaurantId);
            return true;
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    /** @inheritdoc */
    public function getRestaurantUri($restaurantId)
    {
        $config = $this->configRestaurant($restaurantId);
        if (isset($config['uri']))
            return $config['uri'];
        throw \RuntimeException('Restaurant has no URI configured');
    }

    /**
     * @inheritdoc
     */
    public function getParsers()
    {
        return $this->parser->supports();
    }

    /**
     * @inheritdoc
     */
    public function isParser($parser)
    {
        return $this->parser->isSupported($parser);
    }


    /**
     * @inheritdoc
     */
    public function getOutputs()
    {
        return $this->output->supports();
    }

    /**
     * @inheritdoc
     */
    public function isOutput($output)
    {
        return $this->output->isSupported($output);
    }


    /**
     * Retrieves menu of selected restaurant and returns it parsed
     * into an inner form.
     *
     * @param $restaurantId
     * @return array
     */
    public function retrieve($restaurantId) {
        $configuration = $this->configRestaurant($restaurantId);
        $client = new Client();
        $response = $client->request('GET', $configuration['uri']);
        if (empty($response) || $response->getStatusCode() != 200) {
            // TODO log!
            // TODO exception?
            return [];
        }
        return $this->parser->parse($configuration['parser'], $response->getBody()->getContents());
    }

    /** @inheritdoc */
    public function getRetrieved($restaurantId)
    {
        return false;
    }

    /** inheritdoc */
    public function invalidate($restaurantId)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function output($restaurantId, $output, array $options = []) {
        $menu = $this->retrieve($restaurantId);
        $this->output->send($output, $restaurantId, $menu,
            array_merge(
                (is_array($this->configOutput($output)) ? $this->configOutput($output) : []),
                $options
            )
        );
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @param ParserInterface $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * Returns an array with configuration of all known restaurants. Array
     * is stuctured as follows:
     *
     *   [ restaurantId => [ 'uri' => , 'parser' => ] ]
     *
     * @return array
     * @throws \RuntimeException if no restaurant is configured
     */
    protected function configRestaurants()
    {
        if (!isset($this->configuration['restaurants'])) {
            throw new \RuntimeException('No restaurants are configured.');
        }
        return $this->configuration['restaurants'];
    }

    /**
     * Returns an array containing configuration of a restaurant. Array is
     * structured as follows:
     *
     *   [ 'uri' => , 'parser' => ]
     *
     * @param string $restaurantId
     * @return array
     * @throws \RuntimeException if no configuration exists under restaurantId
     */
    protected function configRestaurant($restaurantId)
    {
        if (!isset($this->configuration['restaurants'][$restaurantId])) {
            throw new \RuntimeException('No such restaurant '. $restaurantId .' is configured.');
        }
        return $this->configuration['restaurants'][$restaurantId];
    }

    /**
     * Returns an array with configuration of all known outputs. Array
     * is stuctured as follows:
     *
     *   [ output => [ output_options ] ]
     *
     * @return array
     * @throws \RuntimeException if no outputs are configured
     */
    protected function configOutputs()
    {
        if (!isset($this->configuration['output'])) {
            throw new \RuntimeException('No outputs are configured.');
        }
        return $this->configuration['output'];
    }

    /**
     * Returns an array containing configuration of an output. Array is
     * structured as follows:
     *
     *   [ output_options ]
     *
     * @param string $output
     * @return array
     * @throws \RuntimeException if no configuration exists under output
     */
    protected function configOutput($output)
    {
        if (!isset($this->configuration['output'][$output])) {
            throw new \RuntimeException('No such output '. $output .' configured.');
        }
        return $this->configuration['output'][$output];
    }

}