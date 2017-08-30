<?php

namespace StudioSite\MonitoringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('studiosite:monitoring:get')
            ->setDescription('Get value of parameter')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL
            )
            ->addArgument(
                'arguments',
                InputArgument::IS_ARRAY
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('name')) {
            $this->printValue($input, $output);
        } else {
            $this->printList($input, $output);
        }
    }

    private function getParameterCollection()
    {
        return $this->getContainer()->get('studiosite_monitoring.parameter_collection');
    }

    private function printList(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('The list of available parameters:');

        $list = $this->getParameterCollection()->getList();
        foreach ($list as $name => $arguments) {
            array_walk($arguments, function (&$argument) {
                $argument = '<'.$argument.'>';
            });
            $output->writeln(sprintf("\t%s %s", $name, implode(' ', $arguments)));
        }
    }

    private function printValue(InputInterface $input, OutputInterface $output)
    {
        $value = $this->getParameterCollection()->getValue($input->getArgument('name'), $input->getArgument('arguments'));
        $output->write($value);
    }
}
