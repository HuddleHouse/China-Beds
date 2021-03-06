<?php

namespace InventoryBundle\Entity;

use AppBundle\Entity\User;
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

    /**
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\PromoKitOrders", mappedBy="productVariants")
     */
    private $promo_kit_orders;


    public function __construct() {
        $this->price_group_prices = new ArrayCollection();
        $this->warehouse_inventory = new ArrayCollection();
        $this->warehouse_inventory_on_hold = new ArrayCollection();
        $this->purchase_order_product_variant = new ArrayCollection();
        $this->stock_transfer_product_variant = new ArrayCollection();
        $this->stock_adjustment_product_variant = new ArrayCollection();
        $this->warranty_claims = new ArrayCollection();
        $this->promo_kit_orders = new ArrayCollection();
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
     * @return Product
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


    /**
     * Add purchaseOrderProductVariant
     *
     * @param \WarehouseBundle\Entity\PurchaseOrderProductVariant $purchaseOrderProductVariant
     *
     * @return ProductVariant
     */
    public function addPurchaseOrderProductVariant(\WarehouseBundle\Entity\PurchaseOrderProductVariant $purchaseOrderProductVariant)
    {
        $this->purchase_order_product_variant[] = $purchaseOrderProductVariant;

        return $this;
    }

    /**
     * Remove purchaseOrderProductVariant
     *
     * @param \WarehouseBundle\Entity\PurchaseOrderProductVariant $purchaseOrderProductVariant
     */
    public function removePurchaseOrderProductVariant(\WarehouseBundle\Entity\PurchaseOrderProductVariant $purchaseOrderProductVariant)
    {
        $this->purchase_order_product_variant->removeElement($purchaseOrderProductVariant);
    }

    /**
     * Add stockTransferProductVariant
     *
     * @param \WarehouseBundle\Entity\StockTransferProductVariant $stockTransferProductVariant
     *
     * @return ProductVariant
     */
    public function addStockTransferProductVariant(\WarehouseBundle\Entity\StockTransferProductVariant $stockTransferProductVariant)
    {
        $this->stock_transfer_product_variant[] = $stockTransferProductVariant;

        return $this;
    }

    /**
     * Remove stockTransferProductVariant
     *
     * @param \WarehouseBundle\Entity\StockTransferProductVariant $stockTransferProductVariant
     */
    public function removeStockTransferProductVariant(\WarehouseBundle\Entity\StockTransferProductVariant $stockTransferProductVariant)
    {
        $this->stock_transfer_product_variant->removeElement($stockTransferProductVariant);
    }

    /**
     * Add ordersProductVariant
     *
     * @param \OrderBundle\Entity\OrdersProductVariant $ordersProductVariant
     *
     * @return ProductVariant
     */
    public function addOrdersProductVariant(\OrderBundle\Entity\OrdersProductVariant $ordersProductVariant)
    {
        $this->orders_product_variant[] = $ordersProductVariant;

        return $this;
    }

    /**
     * Remove ordersProductVariant
     *
     * @param \OrderBundle\Entity\OrdersProductVariant $ordersProductVariant
     */
    public function removeOrdersProductVariant(\OrderBundle\Entity\OrdersProductVariant $ordersProductVariant)
    {
        $this->orders_product_variant->removeElement($ordersProductVariant);
    }

    /**
     * Add stockAdjustmentProductVariant
     *
     * @param \WarehouseBundle\Entity\StockAdjustmentProductVariant $stockAdjustmentProductVariant
     *
     * @return ProductVariant
     */
    public function addStockAdjustmentProductVariant(\WarehouseBundle\Entity\StockAdjustmentProductVariant $stockAdjustmentProductVariant)
    {
        $this->stock_adjustment_product_variant[] = $stockAdjustmentProductVariant;

        return $this;
    }

    /**
     * Remove stockAdjustmentProductVariant
     *
     * @param \WarehouseBundle\Entity\StockAdjustmentProductVariant $stockAdjustmentProductVariant
     */
    public function removeStockAdjustmentProductVariant(\WarehouseBundle\Entity\StockAdjustmentProductVariant $stockAdjustmentProductVariant)
    {
        $this->stock_adjustment_product_variant->removeElement($stockAdjustmentProductVariant);
    }

    /**
     * Add priceGroupPrice
     *
     * @param \AppBundle\Entity\PriceGroupPrices $priceGroupPrice
     *
     * @return ProductVariant
     */
    public function addPriceGroupPrice(\AppBundle\Entity\PriceGroupPrices $priceGroupPrice)
    {
        $this->price_group_prices[] = $priceGroupPrice;

        return $this;
    }

    /**
     * Remove priceGroupPrice
     *
     * @param \AppBundle\Entity\PriceGroupPrices $priceGroupPrice
     */
    public function removePriceGroupPrice(\AppBundle\Entity\PriceGroupPrices $priceGroupPrice)
    {
        $this->price_group_prices->removeElement($priceGroupPrice);
    }

    /**
     * Add warehouseInventory
     *
     * @param \WarehouseBundle\Entity\WarehouseInventory $warehouseInventory
     *
     * @return ProductVariant
     */
    public function addWarehouseInventory(\WarehouseBundle\Entity\WarehouseInventory $warehouseInventory)
    {
        $this->warehouse_inventory[] = $warehouseInventory;

        return $this;
    }

    /**
     * Remove warehouseInventory
     *
     * @param \WarehouseBundle\Entity\WarehouseInventory $warehouseInventory
     */
    public function removeWarehouseInventory(\WarehouseBundle\Entity\WarehouseInventory $warehouseInventory)
    {
        $this->warehouse_inventory->removeElement($warehouseInventory);
    }

    /**
     * Add warehouseInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehouseInventoryOnHold $warehouseInventoryOnHold
     *
     * @return ProductVariant
     */
    public function addWarehouseInventoryOnHold(\WarehouseBundle\Entity\WarehouseInventoryOnHold $warehouseInventoryOnHold)
    {
        $this->warehouse_inventory_on_hold[] = $warehouseInventoryOnHold;

        return $this;
    }

    /**
     * Remove warehouseInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehouseInventoryOnHold $warehouseInventoryOnHold
     */
    public function removeWarehouseInventoryOnHold(\WarehouseBundle\Entity\WarehouseInventoryOnHold $warehouseInventoryOnHold)
    {
        $this->warehouse_inventory_on_hold->removeElement($warehouseInventoryOnHold);
    }

    /**
     * Add warrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $warrantyClaim
     *
     * @return ProductVariant
     */
    public function addWarrantyClaim(\InventoryBundle\Entity\WarrantyClaim $warrantyClaim)
    {
        $this->warranty_claims[] = $warrantyClaim;

        return $this;
    }

    /**
     * Remove warrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $warrantyClaim
     */
    public function removeWarrantyClaim(\InventoryBundle\Entity\WarrantyClaim $warrantyClaim)
    {
        $this->warranty_claims->removeElement($warrantyClaim);
    }

    /**
     * @return mixed
     */
    public function getPromoKitOrders()
    {
        return $this->promo_kit_orders;
    }

    /**
     * @param mixed $promo_kit_orders
     */
    public function setPromoKitOrders($promo_kit_orders)
    {
        $this->promo_kit_orders = $promo_kit_orders;
    }

    /**
     * Get warehouseInventory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWarehouseInventory()
    {
        return $this->warehouse_inventory;
    }

    /**
     * Add promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     *
     * @return ProductVariant
     */
    public function addPromoKitOrder(\InventoryBundle\Entity\PromoKitOrders $promoKitOrder)
    {
        $this->promo_kit_orders[] = $promoKitOrder;

        return $this;
    }

    /**
     * Remove promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     */
    public function removePromoKitOrder(\InventoryBundle\Entity\PromoKitOrders $promoKitOrder)
    {
        $this->promo_kit_orders->removeElement($promoKitOrder);
    }

    public function getTotalInventory(User $user = null) {
        $total = 0;
        foreach($this->getWarehouseInventory() as $warehouse_inventory) {
            if ( $user == null || ($user && in_array($warehouse_inventory->getWarehouse(), $user->getWarehouses()->toArray()))) {
                $total += $warehouse_inventory->getQuantity();
            }
        }
        return $total;
    }
    
    public function toArrayWithoutRelation() {

        return [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'sku'           => $this->getSku(),
            'weight'        => $this->getWeight(),
            'dimensions'    => $this->getFedexDimensions(),
            'msrp'          => $this->getMsrp()

        ];
    }
    
    public function toArray(User $user = null) {
        $inventory = [];
        foreach($this->getWarehouseInventory() as $warehouse_inventory) {
            if ( $user == null || ($user && in_array($warehouse_inventory->getWarehouse(), $user->getWarehouses()->toArray()))) {
                $inventory[] = $warehouse_inventory->toArray();
            }
        }
        return [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'product'       => $this->getProduct()->toArray(),
            'sku'           => $this->getSku(),
            'weight'        => $this->getWeight(),
            'dimensions'    => $this->getFedexDimensions(),
            'total_on_hand' => $this->getTotalInventory($user),
            'inventory'     => $inventory
        ];
    }
}
