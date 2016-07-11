<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Warranty
 *
 * @ORM\Table(name="warranty")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\WarrantyRepository")
 */
class Warranty
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
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="credit_requested", type="integer", nullable=true)
     */
    private $creditRequested;

    /**
     * @var int
     *
     * @ORM\Column(name="credit_issued", type="integer", nullable=true)
     */
    private $creditIssued;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Warranty
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
     * Set description
     *
     * @param string $description
     *
     * @return Warranty
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
     * Set creditRequested
     *
     * @param integer $creditRequested
     *
     * @return Warranty
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
     * Set creditIssued
     *
     * @param integer $creditIssued
     *
     * @return Warranty
     */
    public function setCreditIssued($creditIssued)
    {
        $this->creditIssued = $creditIssued;

        return $this;
    }

    /**
     * Get creditIssued
     *
     * @return int
     */
    public function getCreditIssued()
    {
        return $this->creditIssued;
    }
}

