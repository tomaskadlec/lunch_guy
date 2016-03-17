<?php
namespace Net\TomasKadlec\d2sBundle\Service\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait ConfigurationTrait
 *
 * Common provider helper methods
 *
 * @package Net\TomasKadlec\d2sBundle\Service\Configuration
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
        $configuration = new D2sConfiguration();
        $config =
            $processor->processConfiguration($configuration, $config, []);
        return $config;
    }

}