<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 10/31/16
 * Time: 16:39
 */

namespace WarehouseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class POReminderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('warehouse:reminder')

            // the short description shown while running "php bin/console list"
            ->setDescription('On the Warehouse ETA date, resend the notification email with full PO information.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Run this every day on a cron.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getContainer()->get('doctrine')->getRepository('WarehouseBundle:PurchaseOrder')->getTodaysPOs() as $po) {
            try {
                $this->getContainer()->get('email_service')->sendETAUpdateEmail($po);
            } catch (\Exception $e) {
                $output->writeln('Error: ' . $e->getMessage());
            }
        }
        $output->writeln('POReminderCommand complete.');
    }
}