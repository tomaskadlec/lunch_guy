<?php
namespace Net\TomasKadlec\d2sBundle\Service\Configuration;

use League\Flysystem\Filesystem;

/**
 * Class FilesystemProvider
 * @package Net\TomasKadlec\d2sBundle\Service\Configuration
 */
class FilesystemProvider implements ConfigurationProviderInterface
{
    use ConfigurationTrait;

    /** @var  Filesystem */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /** @var string */
    protected $id;

    /** @var array */
    protected $config;

    /** @var  number */
    protected $timestamp;

    /**  */
    public function read($id, array $options = [])
    {
        if (!isset($options['refresh']) && $id == $this->id && $this->config != null)
            return $this->config;

        try {
            $data = $this->filesystem->read($this->getPathFromId($id));
            $config = $this->validate($this->parse($data));
            $this->config = $config;
            $this->id = $id;
            $this->timestamp = $this->filesystem->getTimestamp($this->getPathFromId($id));
            return $config;
        } catch (\League\Flysystem\Exception $e) {
            throw $e; //FIXME better handling
        }
    }

    public function write($id, $config, array $options = [])
    {
        $this->id = $id;
        $this->config = $config;
        $this->timestamp = time();
        if (!$this->filesystem->put($this->getPathFromId($id), $this->dump(['d2s' => $config])))
            throw new \RuntimeException("Failed to write configuration $id");
    }

    /**
     * Returns ID of currently loaded configuration
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns current configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns time of last change to the configuration (or time when it was
     * read from filesystem for the last time)
     *
     * @return number
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    protected function getPathFromId($id)
    {
        return "{$id[0]}/{$id[1]}/$id";
    }

}