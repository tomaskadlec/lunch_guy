<?php
namespace Net\TomasKadlec\d2sBundle\Service\Configuration;

use League\Flysystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
        if ($id == $this->id && $this->config != null)
            return $this->config;

        try {
            $data = $this->filesystem->get($this->getPathFromId($id));
            $config = $this->validate($this->parse($data));
            $this->config = $config;
            $this->id = $id;
            $this->timestamp = $this->filesystem->getTimestamp($this->getPathFromId($id));
            return $config;
        } catch (\Exception $e) {
            throw $e; //FIXME better handling
        }
    }

    public function write($id, $config, array $options = [])
    {
        try {
            $this->id = $id;
            $this->config = $config;
            $this->timestamp = time();
            $this->filesystem->put($this->getPathFromId($id), $this->dump($config));
        } catch (\Exception $e) {
            throw $e; // FIXME better handling
        }
    }

    protected function getPathFromId($id)
    {
        return "{$id[0]}/{$id[1]}/$id";
    }

}