<?php

namespace Rbs\Bundle\SalesBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


/**
 * SendSmsCommand
 */
class SendSmsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('nsm:send-sms')
            ->setDescription('Send sms')
            ->setHelp(<<<EOT
                        The <info>pms:send-sms</info> command send sms:
                          <info>php app/console nsm:send-sms</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        #####        Demo Command      #####
        $msg = "msg";
        $cellNumber = "8801915646596";
        $smsSender = $this->getContainer()->get('rbs_erp.sales.service.smssender');
        $smsSender->agentBankInfoSmsAction($msg, $cellNumber);

       $output->writeln(sprintf('ooo <comment>sms sent</comment>'));
    }

}
