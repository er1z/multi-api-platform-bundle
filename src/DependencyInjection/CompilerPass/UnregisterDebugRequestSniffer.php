<?php

namespace Er1z\MultiApiPlatform\DependencyInjection\CompilerPass;


use Er1z\MultiApiPlatform\EventSubscriber\DebugRequestSnifferListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UnregisterDebugRequestSniffer implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if(
            !$container->getParameter('kernel.debug')
            || !$container->getParameter('multi_api_platform.debug_http_listener.enabled')
        ){
            $container->removeDefinition(DebugRequestSnifferListener::class);
        }
    }
}