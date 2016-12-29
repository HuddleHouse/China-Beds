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

class TestShippingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('orders:test-shipping')
            ->setDescription('Testing Shipping')
            ->addArgument('order-id', InputArgument::REQUIRED, 'Order Id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $service = $this->getContainer()->get('shipping.service');

        if ( $order = $em->getRepository('OrderBundle:Orders')->find($input->getArgument('order-id')) ) {
            print_r($service->calculateShipping($order));
        }
    }
}