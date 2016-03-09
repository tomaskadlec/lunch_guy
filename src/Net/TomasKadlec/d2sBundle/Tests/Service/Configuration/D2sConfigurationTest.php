<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 2.3.16
 * Time: 13:33
 */

namespace Net\TomasKadlec\d2sBundle\Tests\Service\Configuration;


use Net\TomasKadlec\d2sBundle\Service\Configuration\D2sConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class D2sConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testValidConfig()
    {
        $config = Yaml::parse($this->validConfig());
        $processor = new Processor();
        $configuration = new D2sConfiguration();
        $processedConfiguration = $processor->processConfiguration($configuration, $config);
        print_r($config);

        $accessor = PropertyAccess::createPropertyAccessor();
        $result = $accessor->getValue($config, '[d2s][restaurants][Na Urale]');
        print_r($result);

        $this->assertTrue(true);
    }

    protected function validConfig()
    {
        $config = <<< CONFIG
d2s:
    restaurants:
        'Na Urale': { display: true }
        'U Topolů': { display: false }
    output:
        slack:
            uri: http://test.slack.com/TOKEN
CONFIG;
        return $config;
    }


    protected function invalidConfig()
    {

    }
}