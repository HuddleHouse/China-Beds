<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PromoKit
 *
 * @ORM\Table(name="promo_kit")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PromoKitRepository")
 */
class PromoKit
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\PromoKitOrders", mappedBy="promoKitItems")
     */
    private $promoKitOrders;

    public function __construct()
    {
        $this->promoKitOrders = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return PromoKit
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PromoKit
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
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getPromoKitOrders()
    {
        return $this->promoKitOrders;
    }

    /**
     * @param mixed $promoKitOrders
     */
    public function setPromoKitOrders($promoKitOrders)
    {
        $this->promoKitOrders = $promoKitOrders;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     *
     * @return PromoKit
     */
    public function addPromoKitOrder(\InventoryBundle\Entity\PromoKitOrders $promoKitOrder)
    {
        $this->promoKitOrders[] = $promoKitOrder;

        return $this;
    }

    /**
     * Remove promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     */
    public function removePromoKitOrder(\InventoryBundle\Entity\PromoKitOrders $promoKitOrder)
    {
        $this->promoKitOrders->removeElement($promoKitOrder);
    }
}
