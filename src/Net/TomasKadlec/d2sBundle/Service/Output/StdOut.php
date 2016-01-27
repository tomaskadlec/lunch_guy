<?php
namespace Net\TomasKadlec\d2sBundle\Service\Output;

use Net\TomasKadlec\d2sBundle\Service\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StdOut
 * @package Net\TomasKadlec\d2sBundle\Service\Output
 */
class StdOut implements OutputInterface
{
    public function isSupported($format)
    {
        return ($format == 'stdout');
    }

    public function supports()
    {
        return [ 'stdout' ];
    }

    public function format($format, $restaurantId, $menu)
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        return Yaml::dump([$restaurantId => $menu], 3, 2);
    }

    public function send($format, $restaurantId, $menu, $options = [])
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        if (is_array($menu))
            $menu = $this->format($format, $restaurantId, $menu);
        echo "$menu";
    }

}