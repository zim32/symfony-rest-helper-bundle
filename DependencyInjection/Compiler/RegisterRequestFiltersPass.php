<?php

namespace Zim\Bundle\SymfonyRestHelperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zim\Bundle\SymfonyRestHelperBundle\Component\RequestFilter\RequestFilterService;

class RegisterRequestFiltersPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $filterServiceDef = $container->getDefinition(RequestFilterService::class);

        foreach ($container->findTaggedServiceIds('app.request_filter') as $id => $tags) {

            $filterServiceDef->addMethodCall('registerFilter', [new Reference($id)]);

        }
    }

}