<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 9/27/16
 * Time: 14:56
 */

namespace OrderBundle\Services;

use AppBundle\Entity\User;
use OrderBundle\Entity\Ledger;

/**
 * Class LedgerService
 * @package OrderBundle\Services
 */
class LedgerService
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $amount
     * @param User $submittedForUser
     * @param User $submittedByUser
     * @param null $description
     * @param string $type check \OrderBundle\Entity\Ledger for the constants that $type accepts, it will be 'Credit' if left null
     * @param null $typeId if this is not a credit request, add the id of the entity that should count towards credit
     *                     however, ledger entities are inherently credit, so no id is needed
     * @return Ledger
     * @throws \Exception
     */
    public function newEntry($amount, User $submittedForUser, User $submittedByUser, $description = null, $type = 'Credit', $typeId = null) {
        $em = $this->container->get('doctrine')->getManager();

        $ledger = new Ledger();
        $ledger->setAmountRequested($amount);
        $ledger->setAmountCredited($amount);
        $ledger->setDatePosted(new \DateTime());
        $ledger->setSubmittedForUser($submittedForUser);
        $ledger->setSubmittedByUser($submittedByUser);
        if($type == 'Credit')
            $ledger->setAchRequested(false);
        $ledger->setDescription($description);
        $ledger->setType($type);

        switch($type) {
            case 'Order':
                $ledger->setOrder($em->getRepository('OrderBundle:Orders')->find($typeId));
                break;
            case 'Rebate':
                $ledger->setRebate($em->getRepository('InventoryBundle:Rebate')->find($typeId));
                break;
            case 'Warranty':
                $ledger->setWarrantyClaim($em->getRepository('InventoryBundle:WarrantyClaim')->find($typeId));
                break;
        }

        $em->persist($ledger);
        $em->flush();


        return $ledger->toArray();
    }
}