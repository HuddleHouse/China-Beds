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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="variants")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\PurchaseOrderProductVariant", mappedBy="product_variant")
     */
    private $purchase_order_product_variant;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PriceGroupPrices", mappedBy="product_variant")
     */
    private $price_group_prices;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarehouseInventory", mappedBy="product_variant")
     */
    private $warehouse;

    public function __construct() {
        $this->price_group_prices = new ArrayCollection();
        $this->warehouse = new ArrayCollection();
        $this->purchase_order_product_variant = new ArrayCollection();
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


}

