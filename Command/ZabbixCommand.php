<?php

namespace StudioSite\MonitoringBundle\Command;


use StudioSite\MonitoringBundle\Parameter\ParameterCollection;
use StudioSite\MonitoringBundle\Zabbix\ConfigRendererInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class ZabbixCommand extends Command
{
    /**
     * @var ConfigRenderInterface
     */
    private $configRenderer;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ParameterCollection
     */
    private $parameterCollection;

    public function setConfigRenderer(ConfigRendererInterface $configRenderer): void
    {
        $this->configRenderer = $configRenderer;
    }

    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    public function setParameterCollection(ParameterCollection $parameterCollection): void
    {
        $this->parameterCollection = $parameterCollection;
    }

    protected function configure()
    {
        $this
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

        $this->filesystem->dumpFile(
            $path,
            $this->configRenderer->render($this->parameterCollection)
        );
    }
}
