<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderProductVariant
 *
 * @ORM\Table(name="orders_warehouse_info")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrdersWarehouseInfoRepository")
 */
class OrdersWarehouseInfo
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
     * @ORM\Column(name="shipped", type="boolean", nullable=true)
     */
    private $shipped = false;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\OrdersProductVariant", inversedBy="warehouse_info")
     * @ORM\JoinColumn(name="orders_product_variant_id", referencedColumnName="id")
     */
    private $orders_product_variant;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="orders_warehouse_info")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    /**
     * OrdersProductVariant constructor.
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersShippingLabel", mappedBy="orders_warehouse_info", cascade={"persist", "remove"})
     */
    private $shipping_labels;

    public function __construct($quantity, $orders_product_variant, $warehouse)
    {
        $this->quantity = $quantity;
        $this->orders_product_variant = $orders_product_variant;
        $this->warehouse = $warehouse;
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
     * @return OrdersProductVariant
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
    public function getOrdersProductVariant()
    {
        return $this->orders_product_variant;
    }

    /**
     * @param mixed $orders_product_variant
     */
    public function setOrdersProductVariant($orders_product_variant)
    {
        $this->orders_product_variant = $orders_product_variant;
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
    public function getShipped()
    {
        return $this->shipped;
    }

    /**
     * @param mixed $shipped
     */
    public function setShipped($shipped)
    {
        $this->shipped = $shipped;
    }



    /**
     * Add shippingLabel
     *
     * @param \OrderBundle\Entity\OrdersShippingLabel $shippingLabel
     *
     * @return OrdersWarehouseInfo
     */
    public function addShippingLabel(\OrderBundle\Entity\OrdersShippingLabel $shippingLabel)
    {
        $shippingLabel->setOrdersWarehouseInfo($this);
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
}
