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

class ImportUsersCommand extends ContainerAwareCommand
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
            ->setName('import:users')
            ->setDescription('Import Users from old system')
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

        // now loop through again and assign users
        if ( $fp = fopen($input->getArgument('filename'), 'r') ) {
            while($row = fgetcsv($fp)) {
                $this->assignExtraInfo($row, $input, $output);
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

        if ( $data[26] ) {
            $sales_rep = $em->getRepository('AppBundle:User')->findOneBy(['old_id' => $data[26]]);
            $user->setMySalesRep($sales_rep);
        }

        if ( $data[29] ) {
            $distributor = $em->getRepository('AppBundle:User')->findOneBy(['old_id' => $data[26]]);
            $user->setMyDistributor($distributor);
        }

        $em->persist($user);
    }

    private function createUserFromArray($data, $input, $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $factory = $this->getContainer()->get('security.encoder_factory');



        if ( !($user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $data[1]))) ) {
            $user = new User();
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($data[2], $user->getSalt());
            $user->setPassword($password);
        }

        if ( in_array($data[6], $this->users) ) {
            $data[6] = 'duplicate-' . rand(0,100) . '-' . $data[6];
        }


        $this->users[] = $data[6];

        $user->setOldId($data[0]);
        $user->addUserChannel($this->channel);
        $user->setUsername($data[1]);
        $user->setFirstName($data[4]);
        $user->setLastName($data[5]);
        $user->setEmail($data[6]);
        $user->setEmailCanonical($data[6]);
        $user->setAddress1($data[7]);
        $user->setAddress2($data[8]);
        $user->setCity($data[9]);
        $user->setState($this->getStateByAbbr($data[10]));
        $user->setZip($data[11]);
        $user->setPhone($data[15]);
        $user->setEnabled($data[30]);

        if ( $data[17] ) {
            if ( !in_array($this->dist_group, $user->getGroups()->toArray()) ) {
                $user->addGroup($this->dist_group);
            }
            $user->addRole(['roles' => 'ROLE_DISTRIBUTOR']);
        }
        if ( $data[18] ) {
            if ( !in_array($this->retailer_group, $user->getGroups()->toArray()) ) {
                $user->addGroup($this->retailer_group);
            }
            $user->addRole(['roles' => 'ROLE_ADMIN']);
        }
        if ( $data[16] ) {
            if ( !in_array($this->admin_group, $user->getGroups()->toArray()) ) {
                $user->addGroup($this->admin_group);
            }
            $user->addRole(['roles' => 'ROLE_RETAILER']);
        }
        if ( $data[24] ) {
            if ( !in_array($this->salesrep_group, $user->getGroups()->toArray()) ) {
                $user->addGroup($this->salesrep_group);
            }
            $user->addRole(['roles' => 'ROLE_SALES_REP']);
        }
        if ( $data[25] ) {
            if ( !in_array($this->salesmanager_group, $user->getGroups()->toArray()) ) {
                $user->addGroup($this->salesmanager_group);
            }
            $user->addRole(['roles' => 'ROLE_SALES_MANAGER']);
        }

        if ( $data[3] ) {
            if ( $warehouse = $this->getWarehouseById($data[3]) ) {
                if ( !in_array($warehouse, $user->getWarehouses()->toArray()) ) {
                    $user->addWarehouse($warehouse);
                }
            }
        }
        $em->persist($user);
//        $em->flush();

        $output->writeln(sprintf("User %s updated/added", $user->getUsername()));
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