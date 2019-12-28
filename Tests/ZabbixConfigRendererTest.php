<?php

namespace Tests;

use StudioSite\MonitoringBundle\Helper\ConsolePathResolver;
use StudioSite\MonitoringBundle\Parameter\ParameterCollection;
use StudioSite\MonitoringBundle\Zabbix\ConfigRenderer;
use PHPUnit\Framework\TestCase;
use Tests\TestService;

class ZabbixConfigRendererTest extends TestCase
{
    public function testConfigRenderer()
    {
        $renderer = new ConfigRenderer($this->buildConsolePathResolver());

        $this->assertEquals(
            file_get_contents(__DIR__ . '/test.conf'),
            $renderer->render($this->buildCollection())
        );
    }

    private function buildCollection(): ParameterCollection
    {
        $collection = new ParameterCollection();
        $service = new TestService();

        $collection->addParameter($service, 'getParameter', 'test.parameter1');
        $collection->addParameter($service, 'getParameterWithArguments', 'test.parameter2');

        return $collection;
    }

    private function buildConsolePathResolver(): ConsolePathResolver
    {
        $resolver = $this->getMockBuilder(ConsolePathResolver::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $resolver->method('getPath')
            ->willReturn('/test/test');

        return $resolver;
    }
}
