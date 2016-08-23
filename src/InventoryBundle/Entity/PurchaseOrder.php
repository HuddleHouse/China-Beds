<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseOrder
 *
 * @ORM\Table(name="purchase_order")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PurchaseOrderRepository")
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Status", inversedBy="purchase_orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\PurchaseOrderProductVariant", mappedBy="purchase_order")
     */
    private $product_variants;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="purchase_orders")
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
     * @return int
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
     * @return mixed
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


}

