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
    protected $destinations = [
        '/etc/zabbix/zabbix_agentd.conf.d',
        '/etc/zabbix/zabbix_agentd.d',
        '/usr/local/etc/zabbix/zabbix_agentd.conf.d'
    ];

    protected function configure()
    {
        $this
            ->setName('studiosite:monitoring:zabbix')
            ->setDescription('Generate zabbix config for collected parameters')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of the config file'
            )
            ->addOption(
                'destination',
                'd',
                InputOption::VALUE_REQUIRED,
                'Destination path for the config file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->getConfigName($input, $output);

        $destination = $this->getConfigDestination($input, $output);
        $destination = rtrim($destination, "/") . '/' . $name;

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Write config to ' . $destination . '? ',
            false,
            '/^(y|j)/i'
        );

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<comment>You canceled write the config file</comment>');
            return;
        }

        $this->writeConfig($destination);
    }

    protected function getConfigName(InputInterface $input, OutputInterface $output)
    {
        return ($input->getArgument('name')) ?: 'symfony.conf';
    }

    protected function getConfigDestination(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('destination')) {
            return $input->getOption('destination');
        } else {
            foreach ($this->destinations as $_destination) {
                if (is_dir($_destination)) {
                    return $_destination;
                }
            }
        }

        $helper = $this->getHelper('question');

        $question = new Question('Please enter destination path of the config file: ');
        $question->setValidator(function ($value) {
            if (!is_dir($value)) {
                throw new \Exception('The destination not exists');
            }

            return $value;
        });

        return $helper->ask($input, $output, $question);
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
