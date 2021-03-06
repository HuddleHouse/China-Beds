<?php

namespace WarehouseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * StockTransfer
 *
 * @ORM\Table(name="stock_adjustments")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\StockAdjustmentRepository")
 */
class StockAdjustment
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
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Status", inversedBy="stock_adjustments")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockAdjustmentProductVariant", mappedBy="stock_adjustment")
     */
    private $product_variants;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="stock_adjustments")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="stock_adjustments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=255, nullable=true)
     */
    private $orderNumber;


    public function __construct() {
        $this->product_variants = new ArrayCollection();
        $this->date = new \DateTime();
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getProductVariants()
    {
        return $this->product_variants;
    }

    /**
     * @param mixed $product_variants
     */
    public function setProductVariants($product_variants)
    {
        $this->product_variants = $product_variants;
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

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }


    

    /**
     * Add productVariant
     *
     * @param \WarehouseBundle\Entity\StockAdjustmentProductVariant $productVariant
     *
     * @return StockAdjustment
     */
    public function addProductVariant(\WarehouseBundle\Entity\StockAdjustmentProductVariant $productVariant)
    {
        $this->product_variants[] = $productVariant;

        return $this;
    }

    /**
     * Remove productVariant
     *
     * @param \WarehouseBundle\Entity\StockAdjustmentProductVariant $productVariant
     */
    public function removeProductVariant(\WarehouseBundle\Entity\StockAdjustmentProductVariant $productVariant)
    {
        $this->product_variants->removeElement($productVariant);
    }
}
