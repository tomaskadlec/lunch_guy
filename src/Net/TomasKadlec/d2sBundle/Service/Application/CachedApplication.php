<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 28.1.16
 * Time: 7:24
 */

namespace Net\TomasKadlec\d2sBundle\Service\Application;


class CachedApplication extends Application
{
    /**
     * Retrieves menu from a local cache first. If it fails menu is retrieved from configured
     * URL.
     *
     * @param $restaurantId
     * @return array
     */
    public function retrieve($restaurantId)
    {
        $cached = false;
        if ($cached) {
            $result = [];

        } else {
            $result = parent::retrieve($restaurantId);
            if (!empty($result)) {
                // TODO: save to cache
            }
        }
        return $result;
    }

}