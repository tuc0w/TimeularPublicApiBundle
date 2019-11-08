<?php

namespace Tuc0w\TimeularPublicApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle Configuration.
 *
 * @package Tuc0w\TimeularPublicApiBundle\DependencyInjection
 *
 * @author Andreas "tuc0w" Behrend <andreasbehrend@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('tuc0w_timeular_public_api');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('timeular')
                    ->children()
                        ->scalarNode('api_base_url')
                            ->defaultValue('https://api.timeular.com')
                        ->end()
                        ->scalarNode('api_key')
                            ->defaultValue('your_api_key')
                        ->end()
                        ->scalarNode('api_secret')
                            ->defaultValue('your_api_secret')
                        ->end()
                        ->floatNode('api_timeout')
                            ->defaultValue(30.0)
                        ->end()
                        ->scalarNode('api_version')
                            ->defaultValue('/api/v2')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
