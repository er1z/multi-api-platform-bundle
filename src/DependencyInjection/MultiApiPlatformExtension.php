<?php


namespace Er1z\MultiApiPlatform\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MultiApiPlatformExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');

        $container->setParameter('multi_api_platform.apis', $config['apis']);
        $container->setParameter('multi_api_platform.debug_http_listener.enabled', $config['debug_http_listener']['enabled']);
        $container->setParameter('multi_api_platform.debug_http_listener', $config['debug_http_listener']);

    }
}