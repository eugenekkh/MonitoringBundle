<?php

namespace StudioSite\MonitoringBundle\Zabbix;

use StudioSite\MonitoringBundle\Helper\ConsolePathResolver;
use StudioSite\MonitoringBundle\Parameter\ParameterCollection;

class ConfigRenderer implements ConfigRendererInterface
{
    /**
     * @var ConsolePathResolver
     */
    private $consolePathResolver;

    public function __construct(ConsolePathResolver $consolePathResolver)
    {
        $this->consolePathResolver = $consolePathResolver;
    }

    public function render(ParameterCollection $collection): string
    {
        $rows = [];

        foreach ($collection->getList() as $key => $arguments) {
            $rows[] = sprintf(
                'UserParameter=%s[*], %s studiosite:monitoring:get %s%s',
                $key,
                $this->consolePathResolver->getPath(),
                $key,
                $this->buildArguments($arguments)
            );
        }

        return implode(PHP_EOL, $rows) . PHP_EOL;
    }

    private function buildArguments(array $arguments): string
    {
        if (0 == count($arguments)) {
            return '';
        }

        $i = 1;
        $items = [];

        foreach ($arguments as &$argument) {
            $items[] = '$' . $i++;
        }

        return ' '.implode(' ', $items);
    }
}
