<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 2.3.16
 * Time: 13:06
 */

namespace Net\TomasKadlec\d2sBundle\Service\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class D2sConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('d2s');
        $root->append($this->configRestaurants());
        $root->append($this->configOutputs());
        return $treeBuilder;
    }

    protected function configRestaurants()
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $node */
        $node = $treeBuilder->root('restaurants');
        $node
            ->prototype('array')
                ->children()
                    ->scalarNode('id')->end()
                    ->booleanNode('display')->end()
                    ->scalarNode('color')->end()
                ->end()
            ->end();
        return $node;
    }

    protected function configOutputs()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('output');
        $node
            ->children()
                ->append($this->configOutputStdOut())
                ->append($this->configOutputSlack())
            ->end();
        return $node;
    }

    protected function configOutputStdOut()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('stdout');
        return $node;
    }

    protected function configOutputSlack()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('slack');
        $node
            ->children()
                ->scalarNode('uri')
                    ->isRequired()
                ->end()
                ->scalarNode('channel')
                    ->defaultValue('#lunch')
                ->end()
                ->scalarNode('bot_name')
                    ->defaultValue('LunchBot')
                ->end()
                ->scalarNode('bot_emoji')
                    ->defaultValue(':stew:')
                ->end()
            ->end();

        return $node;
    }

}