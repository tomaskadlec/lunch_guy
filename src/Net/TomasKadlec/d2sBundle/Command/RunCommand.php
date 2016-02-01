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
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Select an application output', 'stdout')
            ->addOption('slack-channel', 's', InputOption::VALUE_REQUIRED, 'Select a channel when ')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Run on all configured restaurants')
            ->addArgument('restaurants', InputArgument::IS_ARRAY, 'Restaurant(s) to process', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ApplicationInterface $application */
        $application = $this->getContainer()->get('net_tomas_kadlec_d2s.service.application');

        $outputFormat = $input->getOption('output');
        if (!$application->isOutput($outputFormat))
            throw new \RuntimeException('Supported output formats: ' . join(', ', $application->getOutputs()));

        // output options (prefixed with $outputFormat)
        $options = [];
        foreach($input->getOptions() as $option => $value) {
            if (preg_match("/^{$outputFormat}-/", $option)) {
                $option = preg_replace("/^{$outputFormat}-/", '', $option);
                $options[$option] = $value;
            }
        }

        if ($input->getOption('all')) {
            $restaurantIds  = $application->getRestaurants();
        } else {
            $restaurantIds = $input->getArgument('restaurants');
            if (empty($restaurantIds))
                throw new \RuntimeException('Provide one restaurant ID at least or use --all option');
        }

        foreach ($restaurantIds as $restaurantId) {
            if (!$application->isRestaurant($restaurantId))
                continue;
            $application->output($restaurantId, $outputFormat, $options);
        }
    }
}