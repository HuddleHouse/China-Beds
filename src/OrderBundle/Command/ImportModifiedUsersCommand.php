<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 11/18/16
 * Time: 2:46 PM
 */

namespace OrderBundle\Command;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportModifiedUsersCommand extends ContainerAwareCommand
{
    private $dist_group;
    private $admin_group;
    private $retailer_group;
    private $salesrep_group;
    private $salesmanager_group;
    private $warehouses = [];
    private $channel;
    private $states = [];
    private $users = [];

    protected function configure()
    {
        $this
            ->setName('import:modified-users')
            ->setDescription('Import Users from csv')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename')
            ->addArgument('channel-id', InputArgument::REQUIRED, 'Channel Id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->dist_group = $em->getRepository('AppBundle:Role')->findOneByName('Distributor');
        $this->admin_group = $em->getRepository('AppBundle:Role')->findOneByName('Admin');
        $this->retailer_group = $em->getRepository('AppBundle:Role')->findOneByName('Retailer');
        $this->salesrep_group = $em->getRepository('AppBundle:Role')->findOneByName('Sales Rep');
        $this->salesmanager_group = $em->getRepository('AppBundle:Role')->findOneByName('Sales Manager');

        $this->channel = $em->getRepository('InventoryBundle:Channel')->find($input->getArgument('channel-id'));

        if ( $fp = fopen($input->getArgument('filename'), 'r') ) {
            while($row = fgetcsv($fp)) {
                $this->createUserFromArray($row, $input, $output);
            }
        }
        fclose($fp);

        $em->flush();
    }

    private function assignExtraInfo($data, $input, $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();

        if ( !($user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $data[1]))) ) {
            return;
        }

        if ( $data[41] ) {
            $sales_rep = $em->getRepository('AppBundle:User')->findOneBy(['old_id' => $data[26]]);
            $user->setMySalesRep($sales_rep);
        }

//        if ( $data[29] ) {
//            $distributor = $em->getRepository('AppBundle:User')->findOneBy(['old_id' => $data[26]]);
//            $user->setMyDistributor($distributor);
//        }

        $em->persist($user);
    }

    private function createUserFromArray($data, $input, $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $factory = $this->getContainer()->get('security.encoder_factory');



        if ( !($user = $em->getRepository('AppBundle:User')->find($data[0])) ) {
            return false;
        }

        $user->addUserChannel($this->channel);
        $user->setUsername($data[1]);
        foreach($user->getPriceGroups() as $price_group) {
            $user->removePriceGroup($price_group);
        }
        if ( $price_group = $em->getRepository('AppBundle:PriceGroup')->find($data[2]) ) {
            $user->addPriceGroup($price_group);
        }

        $user->setEmail($data[3]);
        $user->setEnabled($data[4]);
        $user->setFirstName($data[5]);
//        $user->setLastName($data[6]);
        $user->setAddress1($data[7]);
        $user->setAddress2($data[8]);
        $user->setCity($data[9]);
        if ( $state = $em->getRepository('AppBundle:State')->find($data[11]) ) {
            $user->setState($state);
        }
        $user->setPhone($data[10]);
        $user->setZip($data[11]);

        foreach($user->getWarehouses() as $warehouse) {
            $user->removeWarehouse($warehouse);
        }
        $user->setWarehouse1(null);
        $user->setWarehouse2(null);
        $user->setWarehouse3(null);

        if ( $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($data['13']) ) {
            $user->setWarehouse1($warehouse);
        }
        if ( $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($data['14']) ) {
            $user->setWarehouse2($warehouse);
        }
        if ( $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($data['15']) ) {
            $user->setWarehouse3($warehouse);
        }

        $user->setCompanyName($data[16]);

        if ( $distributor = $em->getRepository('AppBundle:User')->find($data[19]) ) {
            $user->setMyDistributor($distributor);
        }

        if ( $salesrep = $em->getRepository('AppBundle:User')->find($data[20]) ) {
            $user->setMySalesRep($salesrep);
        }

        if ( $sales_manager = $em->getRepository('AppBundle:User')->find($data[21]) ) {
            $user->setMySalesManager($sales_manager);
        }

        $em->persist($user);
//        $em->flush();

        $output->writeln(sprintf("User %s with password %s updated/added", $user->getUsername(), $data[2]));
    }

    private function getWarehouseById($id) {
        if ( !$id ) { return null; }
        if ( isset($this->warehouses[$id]) ) { return $this->warehouses[$id]; }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $this->warehouses[$id] = $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($id);

    }

    private function getStateByAbbr($id) {
        if ( !$id ) { return null; }
        if ( isset($this->states[$id]) ) { return $this->states[$id]; }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $this->states[$id] = $em->getRepository('AppBundle:State')->findOneByAbbreviation($id);

    }
}