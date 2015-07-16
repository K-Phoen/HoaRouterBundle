<?php

namespace Hoa\RouterBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Hoa\RouterBundle\DependencyInjection\Compiler\RoutingResolverPass;

class HoaRouterBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoutingResolverPass());
    }
}
