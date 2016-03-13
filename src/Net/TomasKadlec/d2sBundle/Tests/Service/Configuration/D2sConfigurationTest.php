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
        $this->assertNotEmpty($processedConfiguration);
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

    public function testInvalidConfig()
    {
        $config = Yaml::parse($this->invalidConfig());
        $processor = new Processor();
        $configuration = new D2sConfiguration();
        $this->setExpectedException(\Symfony\Component\Config\Definition\Exception\Exception::class);
        $processor->processConfiguration($configuration, $config);
    }

    protected function invalidConfig()
    {
        $config = <<< CONFIG
d2s:
    restaurants:
        'Na Urale': { display: NE }
        'U Topolů': { display: false }
    output:
        slack:
            uri: http://test.slack.com/TOKEN
CONFIG;
        return $config;
    }
}