<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarrantyCliam
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
     * @ORM\Column(name="date_of_claim", type="datetime", nullable=true)
     */
    private $dateOfClaim;

    /**
     * @var string
     *
     * @ORM\Column(name="ratailer", type="string", length=255, nullable=true)
     */
    private $ratailer;

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
     * @return WarrantyCliam
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
     * @return WarrantyCliam
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
     * Set ratailer
     *
     * @param string $ratailer
     *
     * @return WarrantyCliam
     */
    public function setRatailer($ratailer)
    {
        $this->ratailer = $ratailer;

        return $this;
    }

    /**
     * Get ratailer
     *
     * @return string
     */
    public function getRatailer()
    {
        return $this->ratailer;
    }

    /**
     * Set mattressModelId
     *
     * @param integer $mattressModelId
     *
     * @return WarrantyCliam
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
     * @return WarrantyCliam
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
     * @param integer $creditRequested
     *
     * @return WarrantyCliam
     */
    public function setCreditRequested($creditRequested)
    {
        $this->creditRequested = $creditRequested;

        return $this;
    }

    /**
     * Get creditRequested
     *
     * @return int
     */
    public function getCreditRequested()
    {
        return $this->creditRequested;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WarrantyCliam
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
     * @return WarrantyCliam
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
}

