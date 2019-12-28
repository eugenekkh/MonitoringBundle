<?php

namespace StudioSite\MonitoringBundle\Zabbix;

use StudioSite\MonitoringBundle\Parameter\ParameterCollection;

interface ConfigRendererInterface
{
    public function render(ParameterCollection $parameters): string;
}
