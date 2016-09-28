<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderProductVariant
 *
 * @ORM\Table(name="orders_pop_items")
 * @ORM\Entity()
 */
class OrdersPopItem
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
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\PopItem", inversedBy="orders_pop_item")
     * @ORM\JoinColumn(name="pop_item_id", referencedColumnName="id")
     */
    private $pop_item;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="pop_items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersWarehouseInfo", mappedBy="orders_product_variant")
     */
    private $warehouse_info;


    public function __construct()
    {
        $this->warehouse_info = new ArrayCollection();
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderProductVariant
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
     * Set price
     *
     * @param integer $price
     *
     * @return OrderProductVariant
     */
    public function setPrice($price)
    {
        $this->price = $price * 100;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price / 100;
    }

    /**
     * @return mixed
     */
    public function getPopItem()
    {
        return $this->pop_item;
    }

    /**
     * @param mixed $pop_item
     */
    public function setPopItem($pop_item)
    {
        $this->pop_item = $pop_item;
    }


    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getWarehouseInfo()
    {
        return $this->warehouse_info;
    }

    /**
     * @param mixed $warehouse_info
     */
    public function setWarehouseInfo($warehouse_info)
    {
        $this->warehouse_info = $warehouse_info;
    }

    public function addWarehouseInfo($warehouse_info)
    {
        $this->warehouse_info[] = $warehouse_info;
    }
}

