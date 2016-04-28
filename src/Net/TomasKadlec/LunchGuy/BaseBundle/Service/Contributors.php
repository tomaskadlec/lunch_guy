<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\Service;
use Github\Client;
use Github\ResultPager;

/**
 * Class Contributors
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Service
 */
class Contributors
{

    /** @var Client */
    protected $client;

    /** @var  array */
    protected $config;

    /**
     * Contributors constructor.
     */
    public function __construct($cacheDir, $config)
    {
        if (!isset($config['contributors']['user']) || !isset($config['contributors']['repository'])) {
            throw new \RuntimeException('Contributors missing configuration.');
        }
        $this->config = $config;
        $this->client = new \Github\Client(
            new \Github\HttpClient\CachedHttpClient(array('cache_dir' => "$cacheDir/github-api-cache"))
        );
    }

    public function getContributors($link = null)
    {
        $api = $this->client->api('repo');
        $paginator = new ResultPager($this->client);
        $parameters = [
            $this->config['contributors']['user'],
            $this->config['contributors']['repository'],
        ];

        return [
            'contributors' => $paginator->fetch($api, 'contributors', $parameters),
            'links' => $paginator->getPagination(),
        ];
    }
}