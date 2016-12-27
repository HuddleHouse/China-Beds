<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WarehouseBundle\Entity\WarehouseInventory;

/**
 * OrderProductVariant
 *
 * @ORM\Table(name="orders_product_variant")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrdersProductVariantRepository")
 */
class OrdersProductVariant
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="orders_product_variant")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $product_variant;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="product_variants")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersWarehouseInfo", mappedBy="orders_product_variant", cascade={"persist"})
     */
    private $warehouse_info;

//    /**
//     * OrdersProductVariant constructor.
//     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersShippingLabel", mappedBy="orders_product_variant", cascade={"persist"})
//     */
//    private $shipping_labels;


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

    public function addWarehouseInfo(OrdersWarehouseInfo $warehouse_info)
    {
        $warehouse_info->setOrdersProductVariant($this);
        $this->warehouse_info[] = $warehouse_info;
    }

    /**
     * Remove warehouseInfo
     *
     * @param \OrderBundle\Entity\OrdersWarehouseInfo $warehouseInfo
     */
    public function removeWarehouseInfo(\OrderBundle\Entity\OrdersWarehouseInfo $warehouseInfo)
    {
        $this->warehouse_info->removeElement($warehouseInfo);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->warehouse_info = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shipping_labels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add shippingLabel
     *
     * @param \OrderBundle\Entity\OrdersShippingLabel $shippingLabel
     *
     * @return OrdersProductVariant
     */
    public function addShippingLabel(\OrderBundle\Entity\OrdersShippingLabel $shippingLabel)
    {
        $shippingLabel->setOrdersProductVariant($this);
        $this->shipping_labels[] = $shippingLabel;

        return $this;
    }

    /**
     * Remove shippingLabel
     *
     * @param \OrderBundle\Entity\OrdersShippingLabel $shippingLabel
     */
    public function removeShippingLabel(\OrderBundle\Entity\OrdersShippingLabel $shippingLabel)
    {
        $this->shipping_labels->removeElement($shippingLabel);
    }

    /**
     * Get shippingLabels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippingLabels()
    {
        return $this->shipping_labels;
    }

    public function getTotal() {
        return $this->getQuantity() * $this->getPrice();
    }
}
