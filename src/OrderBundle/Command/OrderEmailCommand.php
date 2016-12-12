<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 11/18/16
 * Time: 2:46 PM
 */

namespace OrderBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrderEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('orders:send:email')
            ->setDescription('Send order emails')
            ->addArgument('order-id', InputArgument::REQUIRED, 'Order Id')
            ->addOption('email', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Which Emails to send', []);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $service = $this->getContainer()->get('email_service');

        if ( $order = $em->getRepository('OrderBundle:Orders')->find($input->getArgument('order-id')) ) {
            if ( in_array('admin', $input->getOption('email')) ) {
                $service->sendAdminOrderNotification($order);
            }
            if ( in_array('customer', $input->getOption('email')) ) {
                $service->sendCustomerOrderNotification($order);
            }
            if ( in_array('warehouse', $input->getOption('email')) ) {
                $service->sendWarehouseOrderNotification($order);
            }
        }
    }
}