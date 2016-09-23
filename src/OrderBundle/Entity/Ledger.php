<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Mapping\ManyToOne;


/**
 * Ledger
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\LedgerRepository")
 */
class Ledger
{
    /************************************
     **** the ways to receive credit ****
     ************************************
     * when adding a type, be sure to   *
     * add it to the if statement in    *
     * setType() or else it won't work! *
     ************************************
     ************************************/

    const TYPE_CLAIM    = 'claim'; //to clarify: warranty claims
    const TYPE_CREDIT   = 'credit';//default
    const TYPE_REBATE   = 'rebate';
    const TYPE_TRANSFER = 'transfer';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="ledgers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="added_ledgers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="added_by_user_id", referencedColumnName="id")
     */
    private $addedByUser;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_requested", type="integer")
     */
    private $amountRequested;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_credited", type="integer", nullable=true)
     */
    private $amountCredited; //is null if not just approved or denied, is 0 is denied

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

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=32)
     */
    private $type = self::TYPE_CREDIT;

    /**
     * @var int
     *
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId; //is null if TYPE_CREDIT


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
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getAddedByUser()
    {
        return $this->addedByUser;
    }

    /**
     * @param \AppBundle\Entity\User $addedByUser
     */
    public function setAddedByUser($addedByUser)
    {
        $this->addedByUser = $addedByUser;
    }

    /**
     * Set amountRequested
     *
     * @param float $amountRequested
     *
     * @return Ledger
     */
    public function setAmount($amountRequested)
    {
        $this->amountRequested = $amountRequested * 100;

        return $this;
    }

    /**
     * Get amountRequested
     *
     * @return float
     */
    public function getAmountRequested()
    {
        return $this->amountRequested / 100;
    }

    /**
     * @return float
     */
    public function getAmountCredited()
    {
        return $this->amountCredited / 100;
    }

    /**
     * @param float $amountCredited
     */
    public function setAmountCredited($amountCredited)
    {
        $this->amountCredited = $amountCredited * 100;
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

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_CLAIM,
                self::TYPE_CREDIT,
                self::TYPE_REBATE,
                self::TYPE_TRANSFER
            )
        ))
            throw new \InvalidArgumentException("Invalid type");

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }
}

