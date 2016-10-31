<?php

namespace WarehouseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseOrder
 *
 * @ORM\Table(name="purchase_order")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\PurchaseOrderRepository")
 */
class PurchaseOrder
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
     * @ORM\Column(name="order_date", type="date", nullable=true)
     */
    private $orderDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stock_due_date", type="date", nullable=true)
     */
    private $stockDueDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="port_eta", type="date", nullable=true)
     */
    private $portEta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_received_date", type="datetime", nullable=true)
     */
    private $orderReceivedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=255, nullable=true)
     */
    private $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column (name="physical_container_number", type="string", nullable=true)
     */
    private $physicalContainerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="factory_order_number", type="string", nullable=true)
     */
    private $factoryOrderNumber;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Status", inversedBy="purchase_orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\PurchaseOrderProductVariant", mappedBy="purchase_order")
     */
    private $product_variants;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="purchase_orders")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="purchase_orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct() {
        $this->product_variants = new ArrayCollection();
        $this->stockDueDate = new \DateTime();
        $this->orderDate = new \DateTime();
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
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return PurchaseOrder
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set stockDueDate
     *
     * @param \DateTime $stockDueDate
     *
     * @return PurchaseOrder
     */
    public function setStockDueDate($stockDueDate)
    {
        $this->stockDueDate = $stockDueDate;

        return $this;
    }

    /**
     * Get stockDueDate
     *
     * @return \DateTime
     */
    public function getStockDueDate()
    {
        return $this->stockDueDate;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return PurchaseOrder
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return PurchaseOrder
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set products
     *
     * @param integer $product_variants
     *
     * @return PurchaseOrder
     */
    public function setProductvariants($product_variants)
    {
        $this->product_variants = $product_variants;

        return $this;
    }

    /**
     * Get products
     *
     * @return int
     */
    public function getProductvariants()
    {
        return $this->product_variants;
    }

    public function addPurchaseOrderProductVariant(PurchaseOrderProductVariant $productVariant) {
        $this->product_variants[] = $productVariant;
        return $this->product_variants;
    }

    /**
     * Set warehouseId
     *
     * @param integer $warehouse
     *
     * @return PurchaseOrder
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouseId
     *
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return PurchaseOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getOrderReceivedDate()
    {
        return $this->orderReceivedDate;
    }

    /**
     * @param \DateTime $orderReceivedDate
     */
    public function setOrderReceivedDate($orderReceivedDate)
    {
        $this->orderReceivedDate = $orderReceivedDate;
    }



    /**
     * Set physicalContainer
     *
     * @param string $physicalContainerNumber
     *
     * @return PurchaseOrder
     */
    public function setPhysicalContainerNumber($physicalContainerNumber)
    {
        $this->physicalContainerNumber = $physicalContainerNumber;

        return $this;
    }

    /**
     * Get physicalContainer
     *
     * @return string
     */
    public function getPhysicalContainerNumber()
    {
        return $this->physicalContainerNumber;
    }

    /**
     * Set factoryOrderNumber
     *
     * @param string $factoryOrderNumber
     *
     * @return PurchaseOrder
     */
    public function setFactoryOrderNumber($factoryOrderNumber)
    {
        $this->factoryOrderNumber = $factoryOrderNumber;

        return $this;
    }

    /**
     * Get factoryOrderNumber
     *
     * @return string
     */
    public function getFactoryOrderNumber()
    {
        return $this->factoryOrderNumber;
    }

    /**
     * Add productVariant
     *
     * @param \WarehouseBundle\Entity\PurchaseOrderProductVariant $productVariant
     *
     * @return PurchaseOrder
     */
    public function addProductVariant(\WarehouseBundle\Entity\PurchaseOrderProductVariant $productVariant)
    {
        $this->product_variants[] = $productVariant;

        return $this;
    }

    /**
     * Remove productVariant
     *
     * @param \WarehouseBundle\Entity\PurchaseOrderProductVariant $productVariant
     */
    public function removeProductVariant(\WarehouseBundle\Entity\PurchaseOrderProductVariant $productVariant)
    {
        $this->product_variants->removeElement($productVariant);
    }

    /**
     * @return \DateTime
     */
    public function getPortEta()
    {
        return $this->portEta;
    }

    /**
     * @param \DateTime $portEta
     */
    public function setPortEta($portEta)
    {
        $this->portEta = $portEta;
    }
}
