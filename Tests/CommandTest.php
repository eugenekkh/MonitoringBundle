<?php

namespace Tests;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Tests\Functional\WebTestCase;

class CommandTest extends WebTestCase
{
    public function testGetCommand()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('studiosite:monitoring:get');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'name' => 'parameter',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('value', $output);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'name' => 'parameter_with_argument',
            'arguments' => ['hello', 'world'],
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('helloworld', $output);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('The list of available parameters', $output);
        $this->assertContains('parameter_with_argument', $output);
    }

    public function testZabbixCommand()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('studiosite:monitoring:zabbix');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'path' => $kernel->getCacheDir().'/test.conf',
            '--force' => true
        ]);

        $output = $commandTester->getDisplay();

        $this->assertFileExists($kernel->getCacheDir().'/test.conf');
    }
}
