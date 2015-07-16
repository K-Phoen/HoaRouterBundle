<?php

namespace Hoa\RouterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged routing.loader services to routing.resolver service.
 */
class RoutingResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('hoa.routing.resolver')) {
            return;
        }

        $definition = $container->getDefinition('hoa.routing.resolver');

        foreach ($container->findTaggedServiceIds('hoa.routing.loader') as $id => $attributes) {
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}
