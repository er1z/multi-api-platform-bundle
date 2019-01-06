<?php
namespace Er1z\MultiApiPlatform\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RouterWarmupBypassApiLoaderPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        // during route cache warm-up we need all routes to be collected
        $def = $container->findDefinition('api_platform.route_loader');

        /**
         * @var $argument Reference
         */
        foreach($def->getArguments() as $k=>$argument) {

            if((string)$argument == 'api_platform.metadata.resource.name_collection_factory') {
                $def->setArgument($k, new Reference(
                    'Er1z\MultiApiPlatform\ApiPlatform\ResourceNameCollectionFactoryDecorator.inner'
                ));
                break;
            }
        }
    }
}