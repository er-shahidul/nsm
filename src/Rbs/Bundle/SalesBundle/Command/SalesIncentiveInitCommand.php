<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SalesIncentiveInitCommand extends ContainerAwareCommand
{
    protected $startDate;
    protected $endDate;
    protected $durationType;

    /** @var  EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('nsm:initiate:sales-incentive')
            ->setDescription('Create Sales Incentive Job Queue')
            ->addOption(
                'durationType',
                null,
                InputOption::VALUE_OPTIONAL,
                'Duration Type (Month or Year)',
                'month'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->durationType = $input->getOption('durationType');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $beanstalkTube = $this->getContainer()->getParameter('beanstalkd_tube');
        $beanstalk = $this->getContainer()->get('leezy.pheanstalk.primary');

        $orders= $this->em->getRepository('RbsSalesBundle:Order')->getOrdersForSalesIncentive($this->durationType);

        foreach ($orders as $order) {
            $beanstalk->useTube('salas_incentive')->put(
                json_encode(
                    array(
                        'orderId'      => $order['orderId'],
                        'durationType' => $order['durationType']
                    )
                )
            );

            $output->writeln('Added Queue of Order: ' . $order['orderId']);
        }

        $output->writeln('DONE');
    }
}