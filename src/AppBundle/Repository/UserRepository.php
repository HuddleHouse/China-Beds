<?php

namespace AppBundle\Repository;
use AppBundle\Entity\User;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Orders;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllDistributorsArray() {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select u.*
	from users u 
		left join role_users ru
			on ru.user_id = u.id
		left join roles r 
			on r.id = ru.role_id
		where r.roles LIKE '%ROLE_DISTRIBUTOR%'");
        $statement->execute();
        $data = $statement->fetchAll();

        $distributors = array();
        foreach($data as $d) {
            $dist = $em->getRepository('AppBundle:User')->find($d['id']);
            $distributors[] = $dist;
        }
        return $distributors;
    }

    public function getAllRetailersArray(Channel $channel = null) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select u.*
	from users u 
		left join role_users ru
			on ru.user_id = u.id
		left join roles r 
			on r.id = ru.role_id
		where r.roles LIKE '%ROLE_RETAILER%'");
        $statement->execute();
        $data = $statement->fetchAll();

        $retailers = array();
        foreach($data as $d) {
            if ( $dist = $em->getRepository('AppBundle:User')->find($d['id']) ) {
                if ( $dist->belongsToChannel($channel) ) {
                    $retailers[] = $dist;
                }
            }

        }
        return $retailers;
    }

    public function canViewOrder(Orders $order, User $user) {
        $is_allowed = 0;
        if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_WAREHOUSE'))
            return 1;
        else if($order->getSubmittedForUser()->getId() == $user->getId())
            return 1;
        else if($order->getSubmittedByUser()->getId() == $user->getId())
            return 1;


        if($user->hasRole('ROLE_DISTRIBUTOR')) {
            foreach($user->getRetailers() as $item) {
                if(!isset($user_ids[$item->getId()])) {
                    $user_ids[$item->getId()] = $item->getId();
                    $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $item));
                    foreach($data as $item)
                        if($item->getId() == $user->getId())
                            return 1;
                }
            }
        }
        if($user->hasRole('ROLE_SALES_REP')){
            foreach($user->getDistributors() as $distributor) {
                if(!isset($user_ids[$distributor->getId()])) {
                    $user_ids[$distributor->getId()] = $distributor->getId();
                    $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $distributor));
                    foreach($data as $item)
                        if($item->getId() == $user->getId())
                            return 1;
                }
                foreach($distributor->getRetailers() as $retailer) {
                    if(!isset($user_ids[$retailer->getId()])) {
                        $user_ids[$retailer->getId()] = $retailer->getId();
                        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $retailer));
                        foreach($data as $item)
                            if($item->getId() == $user->getId())
                                return 1;
                    }
                }
            }
        }
        if($user->hasRole('ROLE_SALES_MANAGER')) {
            foreach($user->getSalesReps() as $salesRep) {
                if(!isset($user_ids[$salesRep->getId()])) {
                    $user_ids[$salesRep->getId()] = $salesRep->getId();
                    $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $salesRep));
                    foreach($data as $item)
                        if($item->getId() == $user->getId())
                            return 1;
                }
                foreach($salesRep->getDistributors() as $distributor) {
                    if(!isset($user_ids[$distributor->getId()])) {
                        $user_ids[$distributor->getId()] = $distributor->getId();
                        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $distributor));
                        foreach($data as $item)
                            if($item->getId() == $user->getId())
                                return 1;
                    }
                    foreach($distributor->getRetailers() as $retailer) {
                        if(!isset($user_ids[$retailer->getId()])) {
                            $user_ids[$retailer->getId()] = $retailer->getId();
                            $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $retailer));
                            foreach($data as $item)
                                if($item->getId() == $user->getId())
                                    return 1;
                        }
                    }
                }
            }

        }

        return $is_allowed;
    }

    /**
     * @param User $user
     * @return array|\OrderBundle\Entity\Orders[]
     *
     * This gets the latest orders for a user according to their Roles
     */
    public function getLatestOrdersForUser(User $user) {
        $em = $this->getEntityManager();
        $user_ids = array();
        $user_ids[$user->getId()] = $user->getId();
        $orders = array();
//        $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));

        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $user, 'channel' => $user->getActiveChannel()));
        foreach($data as $item)
            $orders[] = $item;

        if($user->hasRole('ROLE_ADMIN') ) {
            $orders = $em->getRepository('OrderBundle:Orders')->findBy(['channel' => $user->getActiveChannel()]);
        }
        else if($user->hasRole('ROLE_WAREHOUSE')) {
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $orders = $em->getRepository('OrderBundle:Orders')->findBy(array('status' => $status, 'channel' => $user->getActiveChannel()));
        }
        else {

            if($user->hasRole('ROLE_DISTRIBUTOR')) {
                foreach($user->getRetailers() as $item) {
                    if(!isset($user_ids[$item->getId()])) {
                        $user_ids[$item->getId()] = $item->getId();
                        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $item, 'channel' => $user->getActiveChannel()));
                        foreach($data as $item)
                            $orders[] = $item;
                    }
                }
            }
            if($user->hasRole('ROLE_SALES_REP')){
                foreach($user->getDistributors() as $distributor) {
                    if(!isset($user_ids[$distributor->getId()])) {
                        $user_ids[$distributor->getId()] = $distributor->getId();
                        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $distributor, 'channel' => $user->getActiveChannel()));
                        foreach($data as $item)
                            $orders[] = $item;
                    }
                    foreach($distributor->getRetailers() as $retailer) {
                        if(!isset($user_ids[$retailer->getId()])) {
                            $user_ids[$retailer->getId()] = $retailer->getId();
                            $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $retailer, 'channel' => $user->getActiveChannel()));
                            foreach($data as $item)
                                $orders[] = $item;
                        }
                    }
                }
            }
            if($user->hasRole('ROLE_SALES_MANAGER')) {
                foreach($user->getSalesReps() as $salesRep) {
                    if(!isset($user_ids[$salesRep->getId()])) {
                        $user_ids[$salesRep->getId()] = $salesRep->getId();
                        $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $salesRep, 'channel' => $user->getActiveChannel()));
                        foreach($data as $item)
                            $orders[] = $item;
                    }
                    foreach($salesRep->getDistributors() as $distributor) {
                        if(!isset($user_ids[$distributor->getId()])) {
                            $user_ids[$distributor->getId()] = $distributor->getId();
                            $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $distributor, 'channel' => $user->getActiveChannel()));
                            foreach($data as $item)
                                $orders[] = $item;
                        }
                        foreach($distributor->getRetailers() as $retailer) {
                            if(!isset($user_ids[$retailer->getId()])) {
                                $user_ids[$retailer->getId()] = $retailer->getId();
                                $data = $em->getRepository('OrderBundle:Orders')->findBy(array('submitted_for_user' => $retailer, 'channel' => $user->getActiveChannel()));
                                foreach($data as $item)
                                    $orders[] = $item;
                            }
                        }
                    }
                }

            }
        }

        return $orders;
    }

    /**
     * @param Channel $channel
     * @return array
     *
     * Returns an array of all User Entities that are in the channel
     */
    public function findUsersByChannel(Channel $channel) {
        $em = $this->getEntityManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        $correct_users = array();

        foreach($users as $user) {
            foreach($user->getUserChannels() as $chan) {
                if($chan->getId() == $channel->getId()) {
                    $correct_users[] = $user;
                    break;
                }
            }
        }
        return $correct_users;
    }

    /**
     * @param User $user
     * @return array|\OrderBundle\Entity\Orders[]
     *
     * This gets the latest orders for a user according to their Roles
     */
    public function findByUser(User $user) {
        $em = $this->getEntityManager();
        $user_ids = array();
        $user_ids[$user->getId()] = $user->getId();
        $users = array();
//        $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));

        if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_WAREHOUSE')) {
            return $em->getRepository('AppBundle:User')->findAll();
        }
        else {
            if($user->hasRole('ROLE_DISTRIBUTOR')) {
                foreach($user->getRetailers() as $item) {
                    if(!isset($user_ids[$item->getId()])) {
                        $user_ids[$item->getId()] = $item->getId();
                        $users[] = $item;
                    }
                }
            }
            if($user->hasRole('ROLE_SALES_REP')){
                foreach($user->getDistributors() as $distributor) {
                    if(!isset($user_ids[$distributor->getId()])) {
                        $user_ids[$distributor->getId()] = $distributor->getId();
                        $users[] = $distributor;
                    }
                    foreach($distributor->getRetailers() as $retailer) {
                        if(!isset($user_ids[$retailer->getId()])) {
                            $user_ids[$retailer->getId()] = $retailer->getId();
                            $users[] = $retailer;
                        }
                    }
                }
            }
            if($user->hasRole('ROLE_SALES_MANAGER')) {
                foreach($user->getSalesReps() as $salesRep) {
                    if(!isset($user_ids[$salesRep->getId()])) {
                        $user_ids[$salesRep->getId()] = $salesRep->getId();
                        $users[] = $salesRep;
                    }
                    foreach($salesRep->getDistributors() as $distributor) {
                        if(!isset($user_ids[$distributor->getId()])) {
                            $user_ids[$distributor->getId()] = $distributor->getId();
                            $users[] = $distributor;
                        }
                        foreach($distributor->getRetailers() as $retailer) {
                            if(!isset($user_ids[$retailer->getId()])) {
                                $user_ids[$retailer->getId()] = $retailer->getId();
                                $users[] = $retailer;
                            }
                        }
                    }
                }

            }
        }
        return $users;
    }
}
