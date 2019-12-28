<?php

namespace StudioSite\MonitoringBundle\Helper;

use RuntimeException;

class ConsolePathResolver
{
    private $kernelRootDir;
    private $path;

    public function __construct(string $kernelRootDir, ?string $path = null)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->path = $path;
    }

    public function getPath(): string
    {
        if (!empty($this->path)) {
            return $this->path;
        }

        $path = $this->kernelRootDir . '/../bin/console';
        if (file_exists($path)) {
            return realpath($path);
        }

        $path = $this->kernelRootDir . '/../app/console';
        if (file_exists($path)) {
            return realpath($path);
        }

        throw new RuntimeException('Path to executable file of the console can not be detected. Please set it manual in bundle config (studiosite_monitoring.console_path)');
    }
}
