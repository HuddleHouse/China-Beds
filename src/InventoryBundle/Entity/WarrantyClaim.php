<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use OrderBundle\Entity\Ledger;

/**
 * WarrantyClaim
 *
 * @ORM\Table(name="warranty_claim")
 * @ORM\Entity()
 */
class WarrantyClaim
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_made_aware", type="datetime", nullable=true)
     */
    private $dateMadeAware;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_claim", type="datetime")
     */
    private $dateOfClaim;

    /**
     * @var int
     *
     * @ORM\Column(name="mattress_model_id", type="integer", nullable=true)
     */
    private $mattressModelId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="credit_requested", type="integer", nullable=true)
     */
    private $creditRequested;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="resolution", type="text", nullable=true)
     */
    private $resolution;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="warranty_claims")
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submittedForUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="submitted_warranty_claims")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="warrantyClaim")
     */
    private $ledger;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="warranty_claims")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * WarrantyClaim constructor.
     */
    public function __construct()
    {
        $this->setDateOfClaim(new \DateTime());
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
     * Set dateMadeAware
     *
     * @param \DateTime $dateMadeAware
     *
     * @return WarrantyClaim
     */
    public function setDateMadeAware($dateMadeAware)
    {
        $this->dateMadeAware = $dateMadeAware;

        return $this;
    }

    /**
     * Get dateMadeAware
     *
     * @return \DateTime
     */
    public function getDateMadeAware()
    {
        return $this->dateMadeAware;
    }

    /**
     * Set dateOfClaim
     *
     * @param \DateTime $dateOfClaim
     *
     * @return WarrantyClaim
     */
    public function setDateOfClaim($dateOfClaim)
    {
        $this->dateOfClaim = $dateOfClaim;

        return $this;
    }

    /**
     * Get dateOfClaim
     *
     * @return \DateTime
     */
    public function getDateOfClaim()
    {
        return $this->dateOfClaim;
    }

    /**
     * Set mattressModelId
     *
     * @param integer $mattressModelId
     *
     * @return WarrantyClaim
     */
    public function setMattressModelId($mattressModelId)
    {
        $this->mattressModelId = $mattressModelId;

        return $this;
    }

    /**
     * Get mattressModelId
     *
     * @return int
     */
    public function getMattressModelId()
    {
        return $this->mattressModelId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return WarrantyClaim
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set creditRequested
     *
     * @param float $creditRequested
     *
     * @return WarrantyClaim
     */
    public function setCreditRequested($creditRequested)
    {
        $this->creditRequested = $creditRequested * 100;

        return $this;
    }

    /**
     * Get creditRequested
     *
     * @return float
     */
    public function getCreditRequested()
    {
        return $this->creditRequested / 100;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WarrantyClaim
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
     * Set resolution
     *
     * @param string $resolution
     *
     * @return WarrantyClaim
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @return mixed
     */
    public function getSubmittedForUser()
    {
        return $this->submittedForUser;
    }

    /**
     * @param mixed $submittedForUser
     */
    public function setSubmittedForUser($submittedForUser)
    {
        $this->submittedForUser = $submittedForUser;
    }

    /**
     * @return mixed
     */
    public function getSubmittedByUser()
    {
        return $this->submittedByUser;
    }

    /**
     * @param mixed $submittedByUser
     */
    public function setSubmittedByUser($submittedByUser)
    {
        $this->submittedByUser = $submittedByUser;
    }

    /**
     * @return Ledger
     */
    public function getLedger()
    {
        return $this->ledger;
    }

    /**
     * @param Ledger $ledger
     */
    public function setLedger($ledger)
    {
        $this->ledger = $ledger;
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
        $this->order = $order;
    }
}

