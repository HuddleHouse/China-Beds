<?php

namespace WarehouseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseInventory
 *
 * @ORM\Table(name="warehouse_inventory")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\WarehouseInventoryRepository")
 */
class WarehouseInventory
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="warehouse_inventory")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $product_variant;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="inventory")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

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
     * @return WarehouseInventory
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
     * @return \InventoryBundle\Entity\ProductVariant
     */
    public function getProductVariant()
    {
        return $this->product_variant;
    }

    /**
     * @param mixed $product_variant
     */
    public function setProductVariant($product_variant)
    {
        $this->product_variant = $product_variant;
    }

    /**
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }


}

