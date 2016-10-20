<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use InventoryBundle\Entity\WarrantyClaim;


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

    const TYPE_CREDIT   = 'Credit';   //default
    const TYPE_REBATE   = 'Rebate';
    const TYPE_TRANSFER = 'Transfer';
    const TYPE_CLAIM    = 'Warranty';
    const TYPE_ORDER    = 'Order';

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
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submittedForUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="submitted_ledgers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="credited_ledgers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="credited_by_user_id", referencedColumnName="id")
     */
    private $creditedByUser;

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
    private $amountCredited; //is null if not approved or denied, is 0 if denied

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_posted", type="datetime", nullable=true)
     */
    private $datePosted;

    /**
     * @var bool
     *
     * @ORM\Column(name="ach_requested", type="boolean", nullable=true)
     */
    private $achRequested;

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
     * @ORM\Column(name="phone", type="integer", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="integer", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\WarrantyClaim", inversedBy="ledgers")
     */
    private $warrantyClaim;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="ledgers")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\RebateSubmission", inversedBy="ledgers")
     */
    private $rebateSubmission;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="ledgers")
     */
    private $channel;

    /**
     * Ledger constructor.
     */
    public function __construct()
    {
        $this->setDateCreated(new \DateTime());
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
    public function getSubmittedForUser()
    {
        return $this->submittedForUser;
    }

    /**
     * @param \AppBundle\Entity\User $user
     */
    public function setSubmittedForUser($user)
    {
        $this->submittedForUser = $user;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getSubmittedByUser()
    {
        return $this->submittedByUser;
    }

    /**
     * @param \AppBundle\Entity\User $submittedByUser
     */
    public function setSubmittedByUser($submittedByUser)
    {
        $this->submittedByUser = $submittedByUser;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getCreditedByUser()
    {
        return $this->creditedByUser;
    }

    /**
     * @param \AppBundle\Entity\User $creditedByUser
     */
    public function setCreditedByUser($creditedByUser)
    {
        $this->creditedByUser = $creditedByUser;
    }

    /**
     * Set amountRequested
     *
     * @param float $amountRequested
     *
     * @return Ledger
     */
    public function setAmountRequested($amountRequested)
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Ledger
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDatePosted()
    {
        return $this->datePosted;
    }

    /**
     * @param \DateTime $datePosted
     */
    public function setDatePosted($datePosted)
    {
        $this->datePosted = $datePosted;
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
    public function getIsArchived()
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
        $types = array(
            self::TYPE_CLAIM,
            self::TYPE_CREDIT,
            self::TYPE_REBATE,
            self::TYPE_TRANSFER,
            self::TYPE_ORDER
        );
        if (!in_array($type, $types))
            throw new \InvalidArgumentException("Invalid type. Values must be one of the following: " . implode(', ', $types));

        $this->type = $type;
    }

    /**
     * @return WarrantyClaim
     */
    public function getWarrantyClaim()
    {
        return $this->warrantyClaim;
    }

    /**
     * @param WarrantyClaim $warrantyClaim
     */
    public function setWarrantyClaim($warrantyClaim)
    {
        $this->setType(self::TYPE_CLAIM);
        $this->warrantyClaim = $warrantyClaim;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->setType(self::TYPE_ORDER);
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getRebateSubmission()
    {
        return $this->rebateSubmission;
    }

    /**
     * @param mixed $rebateSubmission
     */
    public function setRebateSubmission($rebateSubmission)
    {
        $this->setType(self::TYPE_REBATE);
        $this->rebateSubmission = $rebateSubmission;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function toArray() {
        $rtn = array(
            'id' => $this->getId(),
            'submittedForUserId' => $this->getSubmittedForUser() ? $this->getSubmittedForUser()->getId() : null,
            'submittedByUserId' => $this->getSubmittedByUser() ? $this->getSubmittedByUser()->getId() : null,
            'creditedByUserId' => $this->getCreditedByUser() ? $this->getCreditedByUser()->getId() : null,
            'amountRequested' => $this->getAmountRequested(),
            'amountCredited' => $this->getAmountCredited(),
            'achRequested' => $this->getAchRequested(),
            'isArchived' => $this->getIsArchived(),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'dateCreated' => $this->getDateCreated()->format('m/d/Y'),
            'datePosted' => $this->getDatePosted() ? $this->getDatePosted()->format('m/d/Y') : null,
            'channel' => $this->getChannel()
        );

        switch($this->getType()) {
            case self::TYPE_ORDER:
                $rtn['order'] = $this->getOrder();
                break;
            case self::TYPE_REBATE:
                $rtn['rebate'] = $this->getRebateSubmission();
                break;
            case self::TYPE_CLAIM:
                $rtn['warrantyClaim'] = $this->getWarrantyClaim();
                break;
        }

        return $rtn;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return Ledger
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param integer $email
     *
     * @return Ledger
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return integer
     */
    public function getEmail()
    {
        return $this->email;
    }
}
