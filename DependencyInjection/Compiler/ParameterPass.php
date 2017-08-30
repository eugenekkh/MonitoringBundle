<?php

namespace StudioSite\MonitoringBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The compiler pass collecting all services that marked tag studiosite_monitoring.parameter. These services will be used as data sources for monitoring
 */
class ParameterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('studiosite_monitoring.parameter_collection')) {
            return;
        }

        $parameterCollection = $container->findDefinition('studiosite_monitoring.parameter_collection');
        $parameters = $container->findTaggedServiceIds('studiosite_monitoring.parameter');

        foreach ($parameters as $service => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['method'])) {
                    throw new LogicException(sprintf('The service "%s" that tagged as studiosite_monitoring.parameter must contain the method'));
                }

                if (!isset($tag['key'])) {
                    throw new LogicException(sprintf('The service "%s" that tagged as studiosite_monitoring.parameter must contain the key'));
                }

                $parameterCollection->addMethodCall('addParameter', [new Reference($service), $tag['method'], $tag['key']]);
            }
        }
    }
}
