<?php
namespace Net\TomasKadlec\d2sBundle\Command;

use GuzzleHttp\Client;
use Net\TomasKadlec\d2sBundle\Service\ApplicationInterface;
use Net\TomasKadlec\d2sBundle\Service\ParserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class RunCommand
 * @package Net\TomasKadlec\d2sBundle\Command
 */
class RunCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('d2s:run')
            ->setDescription('Return menus ...')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Choose output', 'stdout')
            ->addArgument('restaurants', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Restaurant(s) to process', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ApplicationInterface $application */
        $application = $this->getContainer()->get('net_tomas_kadlec_d2s.service_application.application');

        $outputFormat = $input->getOption('output');
        if (!$application->isOutput($outputFormat))
            throw new \RuntimeException('Supported output formats: ' . join(', ', $application->getOutputs()));

        $restaurantIds = $input->getArgument('restaurants');
        foreach ($restaurantIds as $restaurantId) {
            if (!$application->isRestaurant($restaurantId))
                continue;
            $application->output($restaurantId, $outputFormat);
        }
    }
}