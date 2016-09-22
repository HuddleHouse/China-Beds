<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Ledger
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\LedgerRepository")
 */
class Ledger
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="added_by_user_id", type="integer")
     */
    private $addedByUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    private $createdOn;

    /**
     * @var bool
     *
     * @ORM\Column(name="ach_requested", type="boolean")
     */
    private $achRequested = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived = false;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;



    public function __construct()
    {
        $this->setCreatedOn(new \DateTime());
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Ledger
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set addedByUserId
     *
     * @param integer $addedByUserId
     *
     * @return Ledger
     */
    public function setAddedByUserId($addedByUserId)
    {
        $this->addedByUserId = $addedByUserId;

        return $this;
    }

    /**
     * Get addedByUserId
     *
     * @return int
     */
    public function getAddedByUserId()
    {
        return $this->addedByUserId;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Ledger
     */
    public function setAmount($amount)
    {
        $this->amount = $amount * 100;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount / 100;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Ledger
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set achRequested
     *
     * @param boolean $achRequested
     *
     * @return Ledger
     */
    public function setAchRequested($achRequested)
    {
        $this->achRequested = $achRequested;

        return $this;
    }

    /**
     * Get achRequested
     *
     * @return bool
     */
    public function getAchRequested()
    {
        return $this->achRequested;
    }

    /**
     * @return boolean
     */
    public function isIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * @param boolean $isArchived
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Ledger
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

