<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockTransferProductVariant
 *
 * @ORM\Table(name="stock_transfer_product_variants")
 * @ORM\Entity()
 */
class StockTransferProductVariant
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
     * @var int
     *
     * @ORM\Column(name="total_quantity_after", type="integer", nullable=true)
     */
    private $total_quantity_after = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="warehouse_quantity_after", type="integer", nullable=true)
     */
    private $warehouse_quantity_after = 0;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\StockTransfer", inversedBy="product_variants")
     * @ORM\JoinColumn(name="stock_transfer_id", referencedColumnName="id")
     */
    private $stock_transfer;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="stock_transfer_product_variant")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $product_variant;

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
     * @return PurchaseOrderProductVariant
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
    public function getStockTransfer()
    {
        return $this->stock_transfer;
    }

    /**
     * @param mixed $stock_transfer
     */
    public function setStockTransfer($stock_transfer)
    {
        $this->stock_transfer = $stock_transfer;
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
    public function getTotalQuantityAfter()
    {
        return $this->total_quantity_after;
    }

    /**
     * @param mixed $total_quantity_after
     */
    public function setTotalQuantityAfter($total_quantity_after)
    {
        $this->total_quantity_after = $total_quantity_after;
    }

    /**
     * @return mixed
     */
    public function getWarehouseQuantityAfter()
    {
        return $this->warehouse_quantity_after;
    }

    /**
     * @param mixed $warehouse_quantity_after
     */
    public function setWarehouseQuantityAfter($warehouse_quantity_after)
    {
        $this->warehouse_quantity_after = $warehouse_quantity_after;
    }


}

