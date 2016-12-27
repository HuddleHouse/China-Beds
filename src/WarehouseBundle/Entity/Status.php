<?php

namespace WarehouseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\StatusRepository")
 */
class Status
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
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="warranty_allowed", type="boolean")
     */
    private $warranty_allowed = false;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\PurchaseOrder", mappedBy="status")
     */
    private $purchase_orders;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockTransfer", mappedBy="status")
     */
    private $stock_transfers;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockAdjustment", mappedBy="status")
     */
    private $stock_adjustments;



    public function __construct() {
        $this->purchase_orders = new ArrayCollection();
        $this->stock_transfers = new ArrayCollection();
        $this->stock_adjustments = new ArrayCollection();
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
     * @return Status
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

    public function getNameFirstLetter()
    {
        return substr($this->name, 0, 1);
    }
    /**
     * Set color
     *
     * @param string $color
     *
     * @return Status
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return mixed
     */
    public function getPurchaseOrders()
    {
        return $this->purchase_orders;
    }

    /**
     * @param mixed $purchase_orders
     */
    public function setPurchaseOrders($purchase_orders)
    {
        $this->purchase_orders = $purchase_orders;
    }

    /**
     * @return mixed
     */
    public function getStockTransfers()
    {
        return $this->stock_transfers;
    }

    /**
     * @param mixed $stock_transfers
     */
    public function setStockTransfers($stock_transfers)
    {
        $this->stock_transfers = $stock_transfers;
    }

    /**
     * @return mixed
     */
    public function getStockAdjustments()
    {
        return $this->stock_adjustments;
    }

    /**
     * @param mixed $stock_adjustments
     */
    public function setStockAdjustments($stock_adjustments)
    {
        $this->stock_adjustments = $stock_adjustments;
    }



    /**
     * Add purchaseOrder
     *
     * @param \WarehouseBundle\Entity\PurchaseOrder $purchaseOrder
     *
     * @return Status
     */
    public function addPurchaseOrder(\WarehouseBundle\Entity\PurchaseOrder $purchaseOrder)
    {
        $this->purchase_orders[] = $purchaseOrder;

        return $this;
    }

    /**
     * Remove purchaseOrder
     *
     * @param \WarehouseBundle\Entity\PurchaseOrder $purchaseOrder
     */
    public function removePurchaseOrder(\WarehouseBundle\Entity\PurchaseOrder $purchaseOrder)
    {
        $this->purchase_orders->removeElement($purchaseOrder);
    }

    /**
     * Add stockTransfer
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransfer
     *
     * @return Status
     */
    public function addStockTransfer(\WarehouseBundle\Entity\StockTransfer $stockTransfer)
    {
        $this->stock_transfers[] = $stockTransfer;

        return $this;
    }

    /**
     * Remove stockTransfer
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransfer
     */
    public function removeStockTransfer(\WarehouseBundle\Entity\StockTransfer $stockTransfer)
    {
        $this->stock_transfers->removeElement($stockTransfer);
    }

    /**
     * Add stockAdjustment
     *
     * @param \WarehouseBundle\Entity\StockAdjustment $stockAdjustment
     *
     * @return Status
     */
    public function addStockAdjustment(\WarehouseBundle\Entity\StockAdjustment $stockAdjustment)
    {
        $this->stock_adjustments[] = $stockAdjustment;

        return $this;
    }

    /**
     * Remove stockAdjustment
     *
     * @param \WarehouseBundle\Entity\StockAdjustment $stockAdjustment
     */
    public function removeStockAdjustment(\WarehouseBundle\Entity\StockAdjustment $stockAdjustment)
    {
        $this->stock_adjustments->removeElement($stockAdjustment);
    }

    /**
     * @return mixed
     */
    public function getWarrantyAllowed()
    {
        return $this->warranty_allowed;
    }

    /**
     * @param mixed $warranty_allowed
     */
    public function setWarrantyAllowed($warranty_allowed)
    {
        $this->warranty_allowed = $warranty_allowed;
    }
}
