<?php

namespace OrderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LedgerNachaProcessingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ledger-nacha-processing')
            ->setDescription('Generate and Send the NACHA file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = tempnam('/tmp', 'nacha');
        $this->getContainer()->get('ledger.service')->generatePendingNACHAFile($filename);
        die($filename);
//        $this->getContainer()->get('ledger.service')->uploadNACHAFile($filename);
    }

}
