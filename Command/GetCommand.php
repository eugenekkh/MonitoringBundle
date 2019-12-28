<?php

namespace StudioSite\MonitoringBundle\Command;

use StudioSite\MonitoringBundle\Parameter\ParameterCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GetCommand extends Command
{
    /**
     * @var ParameterCollection
     */
    private $parameterCollection;

    public function setParameterCollection(ParameterCollection $parameterCollection): void
    {
        $this->parameterCollection = $parameterCollection;
    }

    protected function configure(): void
    {
        $this
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

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getArgument('name')) {
            $this->printValue($input, $output);
        } else {
            $this->printList($input, $output);
        }
    }

    private function printList(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('The list of available parameters:');

        $list = $this->parameterCollection->getList();
        foreach ($list as $name => $arguments) {
            array_walk($arguments, function (&$argument) {
                $argument = '<'.$argument.'>';
            });
            $output->writeln(sprintf("\t%s %s", $name, implode(' ', $arguments)));
        }
    }

    private function printValue(InputInterface $input, OutputInterface $output): void
    {
        $value = $this->parameterCollection->getValue($input->getArgument('name'), $input->getArgument('arguments'));
        $output->write($value);
    }
}
