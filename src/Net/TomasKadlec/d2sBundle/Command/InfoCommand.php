<?php
namespace Net\TomasKadlec\d2sBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('d2s:info')
            ->setDescription('Return menus ...')
            ->addOption('parsers', null, InputOption::VALUE_NONE, 'Show available parsers')
            ->addOption('outputs', null, InputOption::VALUE_NONE, 'Show available outputs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $showParsers = $input->getOption('parsers');
        $showOutputs = $input->getOption('outputs');
        if ($showParsers || !($showParsers || $showOutputs)) {
            $output->writeln('Parsers: ' . join(' ',
                    $this->getContainer()->get('net_tomas_kadlec_d2s.service_application.application')->getParsers()));
        }

        if ($showOutputs || !($showParsers || $showOutputs)) {
            $output->writeln('Outputs: ' . join(' ',
                    $this->getContainer()->get('net_tomas_kadlec_d2s.service_application.application')->getOutputs()));
        }
    }

}