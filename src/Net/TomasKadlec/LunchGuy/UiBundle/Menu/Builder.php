<?php
namespace Net\TomasKadlec\LunchGuy\UiBundle\Menu;

use Knp\Menu\FactoryInterface;
use Net\TomasKadlec\LunchGuy\BaseBundle\Service\ApplicationInterface;

/**
 * Class Builder
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Menu
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
                'route' => 'net_tomaskadlec_lunchguy_ui_default_index',
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
                'route' => 'net_tomaskadlec_lunchguy_ui_default_restaurant',
                'routeParameters' => [
                    'restaurantId' => $restaurantId,
                    'display' => true,
                ],
            ]);
        }

        $menu
            ->addChild('Obědář.js', [
                'route' => 'net_tomaskadlec_lunchguy_ui_default_indexjs',
                'attributes' => [
                    'title' => 'Kam na oběd?',
                ],
            ]);

        $menu
            ->addChild('O aplikaci', [
                'route' => 'net_tomaskadlec_lunchguy_ui_about_index'
            ]);

        $menu
            ->addChild('Přispěvatelé', [
                'route' => 'net_tomaskadlec_lunchguy_ui_about_contributors'
            ]);

        $menu
            ->addChild('Jak přispět?', [
                'route' => 'net_tomaskadlec_lunchguy_ui_about_contribute'
            ]);

        $menu
            ->addChild('Podmínky služby', [
                'route' => 'net_tomaskadlec_lunchguy_ui_terms_index'
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
                'uri' => 'https://github.com/tomaskadlec/lunch_guy',
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