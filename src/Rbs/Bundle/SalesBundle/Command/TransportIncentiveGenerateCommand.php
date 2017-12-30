<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Doctrine\ORM\EntityManager;
use Rbs\Bundle\SalesBundle\Entity\Incentive;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransportIncentiveGenerateCommand extends ContainerAwareCommand
{
    /** @var  EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('nsm:generate:transport-commission')
            ->setDescription('Generate Transport Commission')
            ->addArgument(
                'deliveryId',
                InputArgument::REQUIRED,
                'Enter Agent Id'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deliveryId = $input->getArgument('deliveryId');

        $this->generateCommission($deliveryId);

        $output->writeln('Done : ' . $deliveryId);
    }

    protected function generateCommission($deliveryId)
    {
        $delivery = $this->em->getRepository('RbsSalesBundle:Delivery')->find($deliveryId);
        $deliveryIncentives = $this->em->getRepository('RbsSalesBundle:DeliveryItem')->getDeliveryIncentive($delivery->getId());
        $agent = $delivery->getOrderRef()->getAgent();
        $amount = $this->em->getRepository('RbsCoreBundle:TransportIncentive')->getTransportIncentive($agent->getUser()->getUpozilla()->getId(), $deliveryIncentives[0]['depoId'], $deliveryIncentives[0]['itemTypeId']);

        $details = "Total quantity ".$deliveryIncentives[0]['quantity'].", item type ".$deliveryIncentives[0]['itemType'];
        $incentive = new Incentive();
        $incentive->setAgent($agent);
        $incentive->setType(Incentive::TRANSPORT);
        $incentive->setAmount($deliveryIncentives[0]['quantity'] * $amount[0]['amount']);
        $incentive->setDuration(Incentive::MONTH);
        $incentive->setDetails($details);
        $incentive->setDate(new \DateTime());

        $this->em->getRepository('RbsSalesBundle:Incentive')->create($incentive);
    }
}