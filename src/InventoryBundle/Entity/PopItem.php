<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PopItem
 *
 * @ORM\Table(name="pop_item")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PopItemRepository")
 */
class PopItem
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
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

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
     * @var int
     *
     * @ORM\Column(name="price_per", type="integer", nullable=true)
     */
    private $pricePer;

    /**
     * @var int
     *
     * @ORM\Column(name="shipping_per", type="integer", nullable=true)
     */
    private $shippingPer;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;


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
     * Set sku
     *
     * @param string $sku
     *
     * @return PopItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PopItem
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
     * @return PopItem
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
     * Set pricePer
     *
     * @param integer $pricePer
     *
     * @return PopItem
     */
    public function setPricePer($pricePer)
    {
        $this->pricePer = $pricePer;

        return $this;
    }

    /**
     * Get pricePer
     *
     * @return int
     */
    public function getPricePer()
    {
        return $this->pricePer;
    }

    /**
     * Set shippingPer
     *
     * @param integer $shippingPer
     *
     * @return PopItem
     */
    public function setShippingPer($shippingPer)
    {
        $this->shippingPer = $shippingPer;

        return $this;
    }

    /**
     * Get shippingPer
     *
     * @return int
     */
    public function getShippingPer()
    {
        return $this->shippingPer;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return PopItem
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
}

