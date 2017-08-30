<?php

namespace StudioSite\MonitoringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ZabbixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('studiosite:monitoring:zabbix')
            ->setDescription('Generate zabbix config for collected parameters')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to the config file'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Dont\'s ask confirmation of create the config file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        if (!$input->getOption('force')) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                'Write config to '.$path.'? ',
                false,
                '/^(y|j)/i'
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<comment>You canceled write the config file</comment>');
                return;
            }
        }

        $this->writeConfig($path);
    }

    protected function writeConfig($destination)
    {
        $parameterCollection = $this->getContainer()->get('studiosite_monitoring.parameter_collection');
        $template = $this->getContainer()->getParameter('studiosite_monitoring.zabbix.template');
        $list = $parameterCollection->getList();
        $content = $this->getContainer()->get('templating')->render($template, [
            'bin_file' => $this->getConsolePath(),
            'list' => $list
        ]);

        $filesystem = $this->getContainer()->get('filesystem');
        $filesystem->dumpFile($destination, $content);
    }

    protected function getConsolePath()
    {
        $path = $this->getContainer()->getParameter('studiosite_monitoring.console_path');

        if ($path) {
            return realpath($path);
        }

        $path = $this->getContainer()->getParameter('kernel.root_dir');

        $path1 = $path.'/../bin/console';
        if (file_exists($path1)) {
            return realpath($path1);
        }

        $path2 = $path.'/../app/console';
        if (file_exists($path2)) {
            return realpath($path2);
        }

        throw new \RuntimeException('Path to executable file of the console can not be detected. Please set it manual in bundle config (studiosite_monitoring.console_path)');
    }
}
