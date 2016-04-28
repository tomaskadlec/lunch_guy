<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service;

/**
 * Class ApplicationInfoService
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service
 */
class ApplicationInfoService
{
    protected $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Reads and returns application version string
     *
     * @return string|bool application version string or false on failure
     */
    public function getVersion()
    {
        $appInfo = $this->load();
        if ($appInfo != false && isset($appInfo->version)) {
            return $appInfo->version;
        }
        return false;
    }

    /**
     * Loads application information form VERSION file (if it exists)
     *
     * @return object|bool application information object or false if file does not exist
     */
    protected function load()
    {
        static $appInfo = null;
        if ($appInfo === null) {
            $path = $this->kernelRootDir . '/../VERSION';
            if (file_exists($path))
                $appInfo = json_decode(file_get_contents($path));
        }
        return $appInfo;
    }

}