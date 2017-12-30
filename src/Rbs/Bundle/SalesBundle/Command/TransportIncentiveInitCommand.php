<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TransportIncentiveInitCommand extends ContainerAwareCommand
{
    protected $startDate;
    protected $endDate;

    /** @var  EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('nsm:initiate:transport-commission')
            ->setDescription('Create Transport Incentive Job Queue');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->startDate = date('Y-m-d', strtotime(date('Y-m') . " -1 month"));
        $this->endDate = date('Y-m-t', strtotime(date('Y-m') . " -1 month"));

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $beanstalkTube = $this->getContainer()->getParameter('beanstalkd_tube');
        $beanstalk = $this->getContainer()->get('leezy.pheanstalk.primary');

        $deliveries = $this->em->getRepository('RbsSalesBundle:Delivery')->getDeliveriesForTransportIncentive($this->startDate, $this->endDate);

        foreach ($deliveries as $delivery) {
            $beanstalk->useTube('transport_commission')->put(
                json_encode(
                    array(
                        'deliveryId'      => $delivery['id']
                    )
                )
            );

            $output->writeln('Added Queue of Delivery: ' . $delivery['id']);
        }

        $output->writeln('DONE');
    }
}