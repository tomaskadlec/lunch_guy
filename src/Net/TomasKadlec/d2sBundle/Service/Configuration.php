<?php
namespace Net\TomasKadlec\d2sBundle\Service;

use Net\TomasKadlec\d2sBundle\Service\Configuration\ConfigurationProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Configuration implements ConfigurationInterface
{

    /** @var  ConfigurationProviderInterface */
    protected $provider;

    /**
     * @param ConfigurationProviderInterface $provider
     */
    public function setProvider(ConfigurationProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /** @inheritdoc */
    public function get($id, $key)
    {
        $config = $this->provider->read($id);
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        try {
            return $accessor->getValue($config, $key);
        } catch (\Exception $e) {
            throw new \RuntimeException("Configuration does not have a key $key or $key is empty.", $e->getCode(), $e);
        }
    }

    /** @inheritdoc */
    public function set($id, $key, $value, array $options = [])
    {
        $config = $this->provider->read($id);
        $accessor = PropertyAccess::createPropertyAccessor();
        try {
            $accessor->setValue($config, $key, $value);
        } catch (\Exception $e) {
            throw new \RuntimeException("Invalid configuration", $e->getCode(), $e);
        }
        $this->provider->write($id, $config);
    }
}