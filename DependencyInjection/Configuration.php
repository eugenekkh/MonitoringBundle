<?php

namespace StudioSite\MonitoringBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('studiosite_monitoring');

        $rootNode
            ->children()
                ->scalarNode('console')->defaultValue(null)->end()
                ->arrayNode('zabbix')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('StudioSiteMonitoringBundle:Zabbix:userParameters.conf.twig')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
