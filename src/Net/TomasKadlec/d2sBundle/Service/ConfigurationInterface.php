<?php
namespace Net\TomasKadlec\d2sBundle\Service;

/**
 * Interface ConfigurationInterface
 *
 * Handles storage and retrieval of personalized configuration
 *
 * @package Net\TomasKadlec\d2sBundle\Service
 */
interface ConfigurationInterface
{
    /**
     * Resolves a value under given key. The key may be structured using PropertyAccess notation
     * (e.g. '[d2s][restaurants[[Na Urale]')
     *
     * @param string $id configuration ID
     * @param string $key
     * @return mixed
     */
    public function get($id, $key);

    /**
     * Stores a value under given key. The key may be structured using PropertyAccess notation
     * (e.g. '[d2s][restaurants[[Na Urale]')
     *
     * @param string $id configuration ID
     * @param string $key
     * @param mixed $value
     * @param array $options
     */
    public function set($id, $key, $value, array $options = []);

}