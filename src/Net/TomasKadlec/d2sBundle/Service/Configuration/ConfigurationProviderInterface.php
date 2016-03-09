<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 3.3.16
 * Time: 13:03
 */

namespace Net\TomasKadlec\d2sBundle\Service\Configuration;


interface ConfigurationProviderInterface
{

    public function read($id, array $options = []);

    public function write($id, $config, array $options = []);


}