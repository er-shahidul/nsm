<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SmsRetrieveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:retrieve')
            ->setDescription('Retrieve SMS and Persist to DB')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $client = new \GuzzleHttp\Client();
        $em = $container->get('doctrine.orm.entity_manager');
        $smsRepo = $em->getRepository('RbsSalesBundle:Sms');
        $apiCredential = $container->getParameter('sms');


        try {
            $res = $client->request('GET', $apiCredential['endPoint'], [
                'auth' => [$apiCredential['username'], $apiCredential['password']]
            ]);

            $html = $res->getBody()->getContents();
            $xml = simplexml_load_string($html);

            foreach ($xml->SMS as $sms) {
                $smsRepo->prepareXmlToObject((array)$sms);
            }

        } catch(\GuzzleHttp\Exception\ClientException $e) {
            /** TODO: Do Log */
        }

        $em->flush();
    }
}