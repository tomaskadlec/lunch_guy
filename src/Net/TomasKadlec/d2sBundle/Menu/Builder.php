<?php
namespace Net\TomasKadlec\d2sBundle\Menu;

use Knp\Menu\FactoryInterface;
use Net\TomasKadlec\d2sBundle\Service\ApplicationInterface;

/**
 * Class Builder
 * @package Net\TomasKadlec\d2sBundle\Menu
 */
class Builder
{

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
    }

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @param ApplicationInterface $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * Returns the main menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getMain()
    {
        $menu = $this->factory->createItem('Obědář', [
                'route' => 'net_tomaskadlec_d2s_default_index',
                'attributes' => [
                    'title' => 'Kam na oběd?',
                ],
            ]);

        $menu
            ->addChild('Restaurace', [
                'display' => false
            ]);
        foreach ($this->application->getRestaurants() as $restaurantId) {
            $menu['Restaurace']->addChild($restaurantId,[
                'route' => 'net_tomaskadlec_d2s_default_restaurant',
                'routeParameters' => [
                    'restaurantId' => $restaurantId,
                    'display' => true,
                ],
            ]);
        }

        $menu
            ->addChild('Podmínky služby', [
                'route' => 'net_tomaskadlec_d2s_terms_index'
            ]);

        ;
        return $menu;
    }

    /**
     * Returns the external menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getExternal()
    {
        $menu = $this->factory->createItem('external');
        $menu
            ->addChild('GitHub', [
                'uri' => 'https://github.com/tomaskadlec/d2s',
                'attributes' => [
                    'icon' => 'fa-github',
                    'no_label' => true,
                ],
            ]);
        $menu
            ->addChild('Slack', [
                'uri' => 'https://ictfit.slack.com/messages/obed/',
                'attributes' => [
                    'icon' => 'fa-slack',
                    'no_label' => true,
                ],
            ]);
        return $menu;
    }

}