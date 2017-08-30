<?php

namespace StudioSite\MonitoringBundle;

use StudioSite\MonitoringBundle\DependencyInjection\Compiler\ParameterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StudioSiteMonitoringBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ParameterPass());
    }
}
