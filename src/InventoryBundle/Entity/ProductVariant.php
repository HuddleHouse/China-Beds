<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductVariant
 *
 * @ORM\Table(name="product_variant")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ProductVariantRepository")
 */
class ProductVariant
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
     * @ORM\Column(name="list_id", type="string", length=255, nullable=true)
     */
    private $listId;

    /**
     * @var int
     *
     * @ORM\Column(name="msrp", type="integer", nullable=true)
     */
    private $msrp = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="fedex_dimensions", type="string", length=255, nullable=true)
     */
    private $fedexDimensions;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="variants")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\PurchaseOrderProductVariant", mappedBy="product_variant")
     */
    private $purchase_order_product_variant;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockTransferProductVariant", mappedBy="product_variant")
     */
    private $stock_transfer_product_variant;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersProductVariant", mappedBy="product_variant")
     */
    private $orders_product_variant;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockAdjustmentProductVariant", mappedBy="product_variant")
     */
    private $stock_adjustment_product_variant;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PriceGroupPrices", mappedBy="product_variant")
     */
    private $price_group_prices;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehouseInventory", mappedBy="product_variant")
     */
    private $warehouse_inventory;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehouseInventoryOnHold", mappedBy="product_variant")
     */
    private $warehouse_inventory_on_hold;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="productVariant")
     */
    private $warranty_claims;


    public function __construct() {
        $this->price_group_prices = new ArrayCollection();
        $this->warehouse_inventory = new ArrayCollection();
        $this->warehouse_inventory_on_hold = new ArrayCollection();
        $this->purchase_order_product_variant = new ArrayCollection();
        $this->stock_transfer_product_variant = new ArrayCollection();
        $this->stock_adjustment_product_variant = new ArrayCollection();
        $this->warranty_claims = new ArrayCollection();
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
     * @return ProductVariant
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

    /**
     * Set listId
     *
     * @param string $listId
     *
     * @return ProductVariant
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Get listId
     *
     * @return string
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getMsrp()
    {
        return $this->msrp / 100;
    }

    /**
     * @param mixed $msrp
     */
    public function setMsrp($msrp)
    {
        $this->msrp = $msrp * 100;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getFedexDimensions()
    {
        return $this->fedexDimensions;
    }

    /**
     * @param mixed $fedexDimensions
     */
    public function setFedexDimensions($fedexDimensions)
    {
        $this->fedexDimensions = $fedexDimensions;
    }

    /**
     * @return mixed
     */
    public function getPriceGroupPrices()
    {
        return $this->price_group_prices;
    }

    /**
     * @param mixed $price_group_prices
     */
    public function setPriceGroupPrices($price_group_prices)
    {
        $this->price_group_prices = $price_group_prices;
    }

    /**
     * @return mixed
     */
    public function getPurchaseOrderProductVariant()
    {
        return $this->purchase_order_product_variant;
    }

    /**
     * @param mixed $purchase_order_product_variant
     */
    public function setPurchaseOrderProductVariant($purchase_order_product_variant)
    {
        $this->purchase_order_product_variant = $purchase_order_product_variant;
    }

    /**
     * @return mixed
     */
    public function getWarehouseInventory()
    {
        return $this->warehouse;
    }

    /**
     * @param mixed $warehouse_inventory
     */
    public function setWarehouseInventory($warehouse_inventory)
    {
        $this->warehouse = $warehouse_inventory;
    }

    /**
     * @return mixed
     */
    public function getStockTransferProductVariant()
    {
        return $this->stock_transfer_product_variant;
    }

    /**
     * @param mixed $stock_transfer_product_variant
     */
    public function setStockTransferProductVariant($stock_transfer_product_variant)
    {
        $this->stock_transfer_product_variant = $stock_transfer_product_variant;
    }

    /**
     * @return mixed
     */
    public function getStockAdjustmentProductVariant()
    {
        return $this->stock_adjustment_product_variant;
    }

    /**
     * @param mixed $stock_adjustment_product_variant
     */
    public function setStockAdjustmentProductVariant($stock_adjustment_product_variant)
    {
        $this->stock_adjustment_product_variant = $stock_adjustment_product_variant;
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
    public function getWarehouseInventoryOnHold()
    {
        return $this->warehouse_inventory_on_hold;
    }

    /**
     * @param mixed $warehouse_inventory_on_hold
     */
    public function setWarehouseInventoryOnHold($warehouse_inventory_on_hold)
    {
        $this->warehouse_inventory_on_hold = $warehouse_inventory_on_hold;
    }

    /**
     * @return mixed
     */
    public function getWarrantyClaims()
    {
        return $this->warranty_claims;
    }

    /**
     * @param mixed $warranty_claims
     */
    public function setWarrantyClaims($warranty_claims)
    {
        $this->warranty_claims = $warranty_claims;
    }

}

