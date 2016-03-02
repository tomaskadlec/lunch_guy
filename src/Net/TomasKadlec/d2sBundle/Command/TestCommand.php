<?php
/**
 * Created by PhpStorm.
 * User: kadleto2
 * Date: 2.3.16
 * Time: 7:13
 */

namespace Net\TomasKadlec\d2sBundle\Command;

use GuzzleHttp\Client;
use Net\TomasKadlec\d2sBundle\Service\Parser\DRest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('d2s:test')
            ->setDescription('Return menus ...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $configuration = $this->getContainer()->getParameter('d2s');
        $configuration = $configuration['restaurants']['Na Urale'];

        $client = new Client();
        $response = $client->request('GET', $configuration['uri']);
        if (empty($response) || $response->getStatusCode() != 200) {
            // TODO log!
            // TODO exception?
            return [];
        }

        $parser = new DRest();
        $result = $parser->parse('drest', $response->getBody()->getContents());
        $output->writeln(print_r($result));

        return 0;
    }

}