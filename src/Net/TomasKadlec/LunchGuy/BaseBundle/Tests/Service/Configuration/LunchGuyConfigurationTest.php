<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 2.3.16
 * Time: 13:33
 */

namespace Net\TomasKadlec\LunchGuy\BaseBundle\Tests\Service\Configuration;


use Net\TomasKadlec\LunchGuy\BaseBundle\Service\Configuration\LunchGuyConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class LunchGuyConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testValidConfig()
    {
        $config = Yaml::parse($this->validConfig());
        $processor = new Processor();
        $configuration = new LunchGuyConfiguration();
        $processedConfiguration = $processor->processConfiguration($configuration, $config);
        $this->assertNotEmpty($processedConfiguration);
    }

    protected function validConfig()
    {
        $config = <<< CONFIG
lunch_guy:
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
        $configuration = new LunchGuyConfiguration();
        $this->setExpectedException(\Symfony\Component\Config\Definition\Exception\Exception::class);
        $processor->processConfiguration($configuration, $config);
    }

    protected function invalidConfig()
    {
        $config = <<< CONFIG
lunch_guy:
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