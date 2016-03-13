<?php
namespace Net\TomasKadlec\d2sBundle\Tests\Service;

use Net\TomasKadlec\d2sBundle\Service\Configuration;
use Net\TomasKadlec\d2sBundle\Service\ConfigurationInterface;
use Net\TomasKadlec\d2sBundle\Service\Configuration\FilesystemProvider;
use Net\TomasKadlec\d2sBundle\Tests\Service\Configuration\FilesystemProviderTest;
use Net\TomasKadlec\Test\TestCase\ApplicationTestCase;

/**
 * Class ConfigurationTest
 * @package Net\TomasKadlec\d2sBundle\Tests\Service
 */
class ConfigurationTest extends ApplicationTestCase
{

    /** @var  Configuration */
    protected $configuration;

    /** @inheritdoc */
    protected function setUp()
    {
        parent::setUp();

        $provider = $this
            ->getMockBuilder(FilesystemProvider::class)
            ->getMock();

        $provider
            ->method('read')
            ->willReturnOnConsecutiveCalls(
                ['restaurants' => ['Na Urale' => ['display' => true]]],
                ['restaurants' => ['Na Urale' => ['display' => true]]],
                ['restaurants' => ['Na Urale' => ['display' => false]]]
            );

        $this->configuration = new Configuration();
        $this->configuration->setProvider($provider);
    }


    public function testProvider()
    {
        $this->configuration = $this->container->get('net_tomas_kadlec_d2s.service.configuration');
        $this->assertNotEmpty($this->configuration);
        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
    }

    public function testRead()
    {
        $this->assertTrue(
            $this->configuration->
                get(FilesystemProviderTest::CONFIG_EXISTING, '[restaurants][Na Urale][display]')
        );

        $this->setExpectedException(\RuntimeException::class);
        $this->configuration->get(
            FilesystemProviderTest::CONFIG_EXISTING,
            '[restaurants][Na Urale][blabla]'
        );
    }

    public function testWrite()
    {
        $configurationReflector = new \ReflectionObject($this->configuration);
        $configurationProviderProperty = $configurationReflector->getProperty('provider');
        $configurationProviderProperty->setAccessible(true);
        /** @var \PHPUnit_Framework_MockObject_MockObject $provider */
        $provider = $configurationProviderProperty->getValue($this->configuration);
        $provider
            ->expects($this->exactly(1))
            ->method('write')
            ->with(
                FilesystemProviderTest::CONFIG_EXISTING,
                ['restaurants' => ['Na Urale' => ['display' => false]]]
            );

        $this->assertTrue(
            $this->configuration->get(
                FilesystemProviderTest::CONFIG_EXISTING,
                '[restaurants][Na Urale][display]'
            )
        );
        $this->configuration->set(
            FilesystemProviderTest::CONFIG_EXISTING,
            '[restaurants][Na Urale][display]',
            false
        );
        $this->assertFalse(
            $this->configuration->get(
                FilesystemProviderTest::CONFIG_EXISTING,
                '[restaurants][Na Urale][display]'
            )
        );
    }

    public function testWriteFail()
    {
        $provider = $this
            ->getMockBuilder(FilesystemProvider::class)
            ->getMock();

        $provider
            ->method('read')
            ->willReturnOnConsecutiveCalls(
                "Invalid configuration (not an array or an object."
            );

        $configuration = new Configuration();
        $configuration->setProvider($provider);

        $this->setExpectedException(\RuntimeException::class);
        $configuration->set(
            FilesystemProviderTest::CONFIG_EXISTING,
            '[restaurants][Na Urale][display]',
            false
        );
    }

}