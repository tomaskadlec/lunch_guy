<?php
namespace Net\TomasKadlec\d2sBundle\Command;

use GuzzleHttp\Client;
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
        /** @var ParserInterface $parserService */
        $parserService = $this->getContainer()->get('net_tomas_kadlec_d2s.service.parser');
        /** @var \Net\TomasKadlec\d2sBundle\Service\OutputInterface $output */
        $outputService = $this->getContainer()->get('net_tomas_kadlec_d2s.service.output');
        /** @var Client $client */
        $client = new Client();

        if (!$this->getContainer()->hasParameter('d2s'))
            throw new \RuntimeException('Missing configuration: d2s');
        $config = $this->getContainer()->getParameter('d2s');

        $outputFormat = $input->getOption('output');
        if (!$outputService->isSupported($outputFormat))
            throw new \RuntimeException('Supported output formats: ' . join(', ', $outputService->supports()));
        if (!isset($config['output'][$outputFormat]))
            throw new \RuntimeException("Missing configuration: d2s.output.{$outputFormat}.");
        $outputOptions = $config['output'][$outputFormat];

        $restaurantIds = $input->getArgument('restaurants');
        foreach ($restaurantIds as $restaurantId) {
            if (!isset($config['restaurants'][$restaurantId]['parser']) || !isset($config['restaurants'][$restaurantId]['uri']))
                throw new \RuntimeException("Missing configuration: d2s.restaurants.{$restaurantId}.{uri,parser}.");
            $restaurantConfig = $config['restaurants'][$restaurantId];
            if (!$parserService->isSupported($restaurantConfig['parser']))
                continue;
            $response = $client->request('GET', $restaurantConfig['uri']);
            $menu = $parserService->parse($restaurantConfig['parser'], $response->getBody()->getContents());
            $outputService->send($outputFormat, $restaurantId, $menu, $outputOptions);
        }
    }
}