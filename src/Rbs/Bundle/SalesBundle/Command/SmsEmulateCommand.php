<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Rbs\Bundle\SalesBundle\Entity\Sms;
use Rbs\Bundle\SalesBundle\Helper\SmsParse;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SmsEmulateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:emulate')
            ->setDescription('Retrieve SMS and Persist to DB')
            ->addArgument(
                'mobileNo',
                InputArgument::REQUIRED,
                'Enter Mobile No'
            )
            ->addArgument(
                'msg',
                InputArgument::REQUIRED,
                'Enter Msg'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $smsParse = new SmsParse($em, $container, $input->getArgument('mobileNo'));

        $sms = new Sms();
        $sms->setMobileNo($input->getArgument('mobileNo'));
        $sms->setMsg($input->getArgument('msg'));
        $sms->setDate(new \DateTime());
        $sms->setSl(rand());
        $sms->setStatus('NEW');

        $response = $smsParse->parse($sms);

        if (!$response) {
            $output->writeln('<error>' . $smsParse->error . '</error>');
        }

        if ($response and is_array($response)) {
            foreach ($response as $key => $value) {
                $output->writeln($key . "\t\t\t" . $value);
            }
        }

    }
}