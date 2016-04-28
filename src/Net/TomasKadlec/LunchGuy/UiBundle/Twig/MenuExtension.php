<?php
namespace Net\TomasKadlec\LunchGuy\UiBundle\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Class MenuExtension
 * https://gist.github.com/fsevestre/b378606c4fd23814278a
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var Helper
     */
    private $helper;
    /**
     * @var MatcherInterface
     */
    private $matcher;
    /**
     * @param Helper           $helper
     * @param MatcherInterface $matcher
     */
    public function __construct(Helper $helper, MatcherInterface $matcher)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
    }
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('knp_menu_get_current_item', array($this, 'getCurrentItem')),
        );
    }
    /**
     * Retrieves the current item.
     *
     * @param ItemInterface|string $menu
     *
     * @return ItemInterface
     */
    public function getCurrentItem($menu)
    {
        $rootItem = $this->helper->get($menu);
        $currentItem = $this->retrieveCurrentItem($rootItem);
        if (null === $currentItem) {
            $currentItem = $rootItem;
        }
        return $currentItem;
    }
    /**
     * @param ItemInterface $item
     *
     * @return ItemInterface|null
     */
    private function retrieveCurrentItem(ItemInterface $item)
    {
        $currentItem = null;
        if ($this->matcher->isCurrent($item)) {
            return $item;
        }
        if ($this->matcher->isAncestor($item)) {
            foreach ($item->getChildren() as $child) {
                $currentItem = $this->retrieveCurrentItem($child);
                if (null !== $currentItem) {
                    break;
                }
            }
        }
        return $currentItem;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

}