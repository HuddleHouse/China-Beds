<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * StockTransfer
 *
 * @ORM\Table(name="stock_transfers")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\StockTransferRepository")
 */
class StockTransfer
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Status", inversedBy="stock_transfers")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\StockTransferProductVariant", mappedBy="stock_transfer")
     */
    private $product_variants;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="stock_transfer_receiving")
     * @ORM\JoinColumn(name="receiving_warehouse_id", referencedColumnName="id")
     */
    private $receiving_warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="stock_transfer_departing")
     * @ORM\JoinColumn(name="departing_warehouse_id", referencedColumnName="id")
     */
    private $departing_warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="stock_transfers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
    public function getReceivingWarehouse()
    {
        return $this->receiving_warehouse;
    }

    /**
     * @param mixed $receiving_warehouse
     */
    public function setReceivingWarehouse($receiving_warehouse)
    {
        $this->receiving_warehouse = $receiving_warehouse;
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
     * @return mixed
     */
    public function getDepartingWarehouse()
    {
        return $this->departing_warehouse;
    }

    /**
     * @param mixed $departing_warehouse
     */
    public function setDepartingWarehouse($departing_warehouse)
    {
        $this->departing_warehouse = $departing_warehouse;
    }


    
}

