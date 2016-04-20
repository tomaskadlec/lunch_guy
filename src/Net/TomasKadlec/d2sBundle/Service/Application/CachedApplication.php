<?php
namespace Net\TomasKadlec\d2sBundle\Service\Application;

use Doctrine\Common\Cache\Cache;

/**
 * Class CachedApplication
 * @package Net\TomasKadlec\d2sBundle\Service\Application
 */
class CachedApplication extends Application
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Retrieves menu from a local cache first. If it fails menu is retrieved from configured
     * URL.
     *
     * @param $restaurantId
     * @return array
     */
    public function retrieve($restaurantId)
    {
        if ($this->cache->contains($restaurantId)) {
            $result = $this->cache->fetch($restaurantId)['data'];

        } else {
            $result = parent::retrieve($restaurantId);
            $lifetime = 60;
            if (!empty($result)) {
                $now = new \DateTime();
                $tomorrow = new \DateTime('tomorrow');
                $lifetime =  $tomorrow->getTimestamp() - $now->getTimestamp() + 3600; // invalidate cache an hour after midnight
            }
            $this->cache->save($restaurantId, [
                'cached' => new \DateTime(),
                'data' => $result
            ], $lifetime);
        }
        return $result;
    }

    /** @inheritdoc */
    public function getRetrieved($restaurantId)
    {
        if ($this->cache->contains($restaurantId)) {
            return $this->cache->fetch($restaurantId)['cached'];
        }
        return false;
    }

    /** @inheritdoc */
    public function invalidate($restaurantId)
    {
        if ($this->cache->contains($restaurantId)) {
            $cached = $this->cache->fetch($restaurantId)['cached']->getTimestamp();
            $now = (new \DateTime())->getTimestamp();
            if ($now - $cached < 180)
                throw new \RuntimeException('Invalidation rate too big.');
            $this->cache->delete($restaurantId);
            return true;
        }
        return false;
    }

}