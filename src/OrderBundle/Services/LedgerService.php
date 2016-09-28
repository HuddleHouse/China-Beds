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

class LedgerService
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $amount
     * @param User $user
     * @param User $addedByUser
     * @param bool|false $achRequested
     * @param null $description
     * @param string $type check \OrderBundle\Entity\Ledger for the constants that $type accepts, it will be 'Credit' if left null
     * @param null $typeId if this is not a credit request, add the id of the entity that should count towards credit
     *                     however, ledger entities are inherently credit, so no id is needed
     * @return Ledger
     * @throws \Exception
     */
    public function newEntry($amount, User $user, User $addedByUser, $achRequested = false, $description = null, $type = 'Credit', $typeId = null) {
        $ledger = new Ledger();
        $ledger->setAmountRequested($amount);
        $ledger->setUser($user);
        $ledger->setAddedByUser($addedByUser);
        $ledger->setAchRequested($achRequested);
        $ledger->setDescription($description);
        $ledger->setType($type);
        $ledger->setTypeId($typeId);

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($ledger);
        $em->flush();

        return $ledger->toArray();
    }
}