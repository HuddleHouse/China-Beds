<?php

namespace WarehouseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseOrderProductVariant
 *
 * @ORM\Table(name="purchase_order_product_variants")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\PurchaseOrderProductVariantRepository")
 */
class PurchaseOrderProductVariant
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
     * @ORM\Column(name="ordered_quantity", type="integer", nullable=true)
     */
    private $ordered_quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="received_quantity", type="integer", nullable=true)
     */
    private $received_quantity = 0;

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
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\PurchaseOrder", inversedBy="product_variants")
     * @ORM\JoinColumn(name="purchase_order_id", referencedColumnName="id")
     */
    private $purchase_order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="purchase_order_product_variant")
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
     * @param integer $ordered_quantity
     *
     * @return PurchaseOrderProductVariant
     */
    public function setOrderedQuantity($ordered_quantity)
    {
        $this->ordered_quantity = $ordered_quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getOrderedQuantity()
    {
        return $this->ordered_quantity;
    }

    /**
     * @return mixed
     */
    public function getPurchaseOrder()
    {
        return $this->purchase_order;
    }

    /**
     * @param mixed $purchase_order
     */
    public function setPurchaseOrder($purchase_order)
    {
        $this->purchase_order = $purchase_order;
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
    public function getReceivedQuantity()
    {
        return $this->received_quantity;
    }

    /**
     * @param mixed $received_quantity
     */
    public function setReceivedQuantity($received_quantity)
    {
        $this->received_quantity = $received_quantity;
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
