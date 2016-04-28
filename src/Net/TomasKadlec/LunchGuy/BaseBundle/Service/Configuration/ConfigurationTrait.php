<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait ConfigurationTrait
 *
 * Common provider helper methods
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service\Configuration
 */
trait ConfigurationTrait
{

    protected function parse($data)
    {
        return Yaml::parse($data);
    }

    protected function dump($data)
    {
        return Yaml::dump($data);
    }

    /**
     * @param $config
     * @return array
     */
    protected function validate($config)
    {
        $processor = new Processor();
        $configuration = new LunchGuyConfiguration();
        $config =
            $processor->processConfiguration($configuration, $config, []);
        return $config;
    }

}