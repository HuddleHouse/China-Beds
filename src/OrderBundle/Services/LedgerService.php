<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 9/27/16
 * Time: 14:56
 */

namespace OrderBundle\Services;

use AppBundle\Entity\User;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Ledger;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class LedgerService
 * @package OrderBundle\Services
 */
class LedgerService
{
    protected $container;

    /**
     * LedgerService constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine'))
            throw new \LogicException('The DoctrineBundle is not registered in your application.');

        return $this->container->get('doctrine');
    }

    /**
     * @param $amount
     * @param User $submittedForUser
     * @param User $submittedByUser
     * @param Channel $channel
     * @param null $description
     * @param string $type check \OrderBundle\Entity\Ledger for the constants that $type accepts, it will be 'Credit' if left null
     * @param null $typeId if this is not a credit request, add the id of the entity that should count towards credit
     *                     however, ledger entities are inherently credit, so no id is needed
     * @param bool|false $returnArray return as an array instead of as an entity object if true
     * @return Ledger
     * @throws \Exception if updating the entities fails
     */
    public function newEntry($amount, User $submittedForUser, User $submittedByUser, Channel $channel, $description = null, $type = 'Credit', $typeId = null, $returnArray = false) {
        $em = $this->getDoctrine()->getManager();

        $ledger = new Ledger();
        $ledger->setAmountRequested($amount);
        $ledger->setSubmittedForUser($submittedForUser);
        $ledger->setSubmittedByUser($submittedByUser);
        $ledger->setChannel($channel);
        if($type == 'Credit')
            $ledger->setAchRequested(false);
        $ledger->setDescription($description);
        $ledger->setType($type);

        //remove these if we ever implement credit approval
        $ledger->setAmountCredited($amount);
        $ledger->setDatePosted(new \DateTime());

        //put the ledger records where they belong
        $submittedByUser->getSubmittedLedgers()->add($ledger);
        $submittedForUser->getLedgers()->add($ledger);
        $channel->getLedgers()->add($ledger);

        switch($type) {
            case 'Order':
                $order = $em->getRepository('OrderBundle:Orders')->find($typeId);
                $ledger->setOrder($order);
                $order->getLedgers()->add($ledger);
                break;
            case 'Rebate':
                $rebateSubmission = $em->getRepository('InventoryBundle:RebateSubmission')->find($typeId);
                $ledger->setRebateSubmission($rebateSubmission);
                $ledger->setIsArchived(true);
                $rebateSubmission->getLedgers()->add($ledger);
                break;
            case 'Warranty':
                $warrantyClaim = $em->getRepository('InventoryBundle:WarrantyClaim')->find($typeId);
                $ledger->setWarrantyClaim($warrantyClaim);
                $ledger->setIsArchived(true);
                $warrantyClaim->getLedgers()->add($ledger);
                break;
        }

        $em->persist($ledger);
        $em->flush();

        if($returnArray)
            return $ledger->toArray();

        return $ledger;
    }
}