<?php


namespace Er1z\MultiApiPlatform\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    const CONFIGURATION_ROOT_KEY = 'multi_api_platform';

    public function getConfigTreeBuilder()
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder(self::CONFIGURATION_ROOT_KEY);
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root(self::CONFIGURATION_ROOT_KEY);
        }

        $rootNode
            ->children()
                ->arrayNode('apis')
                ->useAttributeAsKey('api')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('namespace')->end()
                        ->scalarNode('implements')->end()
                        ->scalarNode('conditions')->end()
                        ->scalarNode('debug_conditions')->end()
                    ->end()
                        ->validate()
                            ->ifTrue(function($a){
                                return empty($a['namespace']) && empty($a['implements']);
                            })
                            ->thenInvalid('Either namespace or implements should be specified')
                        ->end()
                        ->validate()
                            ->ifTrue(function($a){
                                return empty($a['conditions']) || empty($a['debug_conditions']);
                            })
                            ->thenInvalid('"conditions" and "debug_conditions" must be provided')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('debug_http_listener')
                    ->children()
                        ->scalarNode('enabled')->defaultTrue()->end()
                        ->scalarNode('request_param')->defaultValue('x-api-select')->end()
                        ->arrayNode('request_order')
                            ->prototype('scalar')->end()
                            ->defaultValue(['query', 'request', 'cookies', 'headers', 'attributes', 'server'])
                            ->validate()
                                ->ifNotInArray(['request', 'query', 'attributes', 'cookies'])
                                ->thenInvalid('there is no such request property')
                            ->end()
                        ->end()
                        ->scalarNode('set_cookie')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}