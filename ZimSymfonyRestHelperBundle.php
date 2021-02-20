<?php

namespace Zim\Bundle\SymfonyRestHelperBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zim\Bundle\SymfonyRestHelperBundle\DependencyInjection\Compiler\RegisterRequestFiltersPass;

class ZimSymfonyRestHelperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterRequestFiltersPass());
    }

    public function boot()
    {

    }

}