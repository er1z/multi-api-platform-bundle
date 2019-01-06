<?php
/**
 * Created by PhpStorm.
 * User: eRIZ
 * Date: 29.12.2018
 * Time: 12:02
 */

namespace Er1z\MultiApiPlatform\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoverStockSwaggerCommandPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        // we need to cover api:swagger:export command with our stuff
        $def = $container->findDefinition('api_platform.swagger.command.swagger_command');
        $def->clearTag('console.command');
    }
}