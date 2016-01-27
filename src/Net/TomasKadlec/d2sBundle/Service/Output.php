<?php
namespace Net\TomasKadlec\d2sBundle\Service;

/**
 * Class Output
 * @package Net\TomasKadlec\d2sBundle\Service
 */
class Output implements OutputInterface
{
    /**
     * @var OutputInterface[]
     */
    protected $outputs;

    public function __construct()
    {
        $this->outputs = [];
    }

    /**
     * @param OutputInterface $output
     */
    public function add(OutputInterface $output)
    {
        foreach ($output->supports() as $support) {
            $this->outputs[$support] = $output;
        }
    }

    public function isSupported($format)
    {
        return isset($this->outputs[$format]);
    }

    public function supports()
    {
        $supports = [];
        foreach ($this->outputs as $output) {
            $supports = array_merge($supports, $output->supports());
        }
        return $supports;
    }

    public function format($format, $restaurantId, $menu)
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        return $this->outputs[$format]->format($format, $restaurantId, $menu);
    }

    public function send($format, $restaurantId, $menu, $options = [])
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        if (is_array($menu))
            $menu = $this->format($format, $restaurantId, $menu);
        return $this->outputs[$format]->send($format, $restaurantId, $menu, $options);
    }

}