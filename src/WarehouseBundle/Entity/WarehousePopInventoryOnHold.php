<?php

namespace WarehouseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseInventory
 *
 * @ORM\Table(name="warehouse_pop_nventory_on_hold")
 * @ORM\Entity()
 */
class WarehousePopInventoryOnHold
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\PopItem", inversedBy="warehouse_pop_inventory_on_hold")
     * @ORM\JoinColumn(name="pop_item_id", referencedColumnName="id")
     */
    private $pop_item;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="pop_inventory_on_hold")
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
     * @return mixed
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



    /**
     * Set popItem
     *
     * @param \InventoryBundle\Entity\PopItem $popItem
     *
     * @return WarehousePopInventoryOnHold
     */
    public function setPopItem(\InventoryBundle\Entity\PopItem $popItem = null)
    {
        $this->pop_item = $popItem;

        return $this;
    }

    /**
     * Get popItem
     *
     * @return \InventoryBundle\Entity\PopItem
     */
    public function getPopItem()
    {
        return $this->pop_item;
    }
}
