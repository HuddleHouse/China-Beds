<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * PopItem
 *
 * @ORM\Table(name="pop_item")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PopItemRepository")
 */
class PopItem
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
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="price_per", type="integer", nullable=true)
     */
    private $pricePer;

    /**
     * @var int
     *
     * @ORM\Column(name="shipping_per", type="integer", nullable=true)
     */
    private $shippingPer;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="promo_kit_available", type="boolean")
     */
    private $promo_kit_available = false;

    /**
     * @var string
     *
     * @ORM\Column(name="list_id", type="string", length=255, nullable=true)
     */
    private $list_id;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @ORM\Column(name="hide_order", type="boolean", nullable=true)
     */
    public $hide_order;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersPopItem", mappedBy="pop_item")
     */
    private $orders_pop_item;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehousePopInventory", mappedBy="pop_item")
     */
    private $warehouse_pop_inventory;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehousePopInventoryOnHold", mappedBy="pop_item")
     */
    private $warehouse_pop_inventory_on_hold;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="ledgers")
     */
    private $channel;

    /**
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\PromoKitOrders", mappedBy="popItems")
     */
    private $promo_kit_orders;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hide_on_order", type="boolean", nullable=true)
     */
    private $is_hide_on_order;

    /**
     * @ORM\ManyToMany(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="pop_items")
     * @ORM\JoinTable(name="pop_items_warehouses")
     */
    private $warehouses;

    public function __construct()
    {
        $this->warehouse_pop_inventory = new ArrayCollection();
        $this->warehouse_pop_inventory_on_hold = new ArrayCollection();
        $this->orders_pop_item = new ArrayCollection();
        $this->promo_kit_orders = new ArrayCollection();
        $this->warehouses = new ArrayCollection();
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
     * Set sku
     *
     * @param string $sku
     *
     * @return PopItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsHideOnOrder()
    {
        return $this->is_hide_on_order;
    }

    /**
     * @param boolean $is_hide_on_order
     */
    public function setIsHideOnOrder($is_hide_on_order)
    {
        $this->is_hide_on_order = $is_hide_on_order;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PopItem
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
     * Set description
     *
     * @param string $description
     *
     * @return PopItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set pricePer
     *
     * @param integer $pricePer
     *
     * @return PopItem
     */
    public function setPricePer($pricePer)
    {
        $this->pricePer = $pricePer*100;

        return $this;
    }

    /**
     * Get pricePer
     *
     * @return int
     */
    public function getPricePer()
    {
        return $this->pricePer/100;
    }

    /**
     * Set shippingPer
     *
     * @param integer $shippingPer
     *
     * @return PopItem
     */
    public function setShippingPer($shippingPer)
    {
        $this->shippingPer = $shippingPer*100;

        return $this;
    }

    /**
     * Get shippingPer
     *
     * @return int
     */
    public function getShippingPer()
    {
        return $this->shippingPer/100;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return PopItem
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return boolean
     */
    public function isPromoKitAvailable()
    {
        return $this->promo_kit_available;
    }

    /**
     * @param boolean $promo_kit_available
     */
    public function setPromoKitAvailable($promo_kit_available)
    {
        $this->promo_kit_available = $promo_kit_available;
    }

    /**
     * @return string
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * @param string $list_id
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $newName = md5(date('Y-m-d H:i:s:u')) . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $newName
        );

        // set the path property to the filename where you've saved the file
        $this->path = $newName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $tmp = __DIR__ . '/../../../web/' . $this->getUploadDir();
        return $tmp;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getOrdersPopItem()
    {
        return $this->orders_pop_item;
    }

    /**
     * @param mixed $orders_pop_item
     */
    public function setOrdersPopItem($orders_pop_item)
    {
        $this->orders_pop_item = $orders_pop_item;
    }

    /**
     * @return mixed
     */
    public function getWarehousePopInventory()
    {
        return $this->warehouse_pop_inventory;
    }

    /**
     * @param mixed $warehouse_pop_inventory
     */
    public function setWarehousePopInventory($warehouse_pop_inventory)
    {
        $this->warehouse_pop_inventory = $warehouse_pop_inventory;
    }

    /**
     * @return mixed
     */
    public function getWarehousePopInventoryOnHold()
    {
        return $this->warehouse_pop_inventory_on_hold;
    }

    /**
     * @param mixed $warehouse_pop_inventory_on_hold
     */
    public function setWarehousePopInventoryOnHold($warehouse_pop_inventory_on_hold)
    {
        $this->warehouse_pop_inventory_on_hold = $warehouse_pop_inventory_on_hold;
    }



    /**
     * Add ordersPopItem
     *
     * @param \OrderBundle\Entity\OrdersPopItem $ordersPopItem
     *
     * @return PopItem
     */
    public function addOrdersPopItem(\OrderBundle\Entity\OrdersPopItem $ordersPopItem)
    {
        $this->orders_pop_item[] = $ordersPopItem;

        return $this;
    }

    /**
     * Remove ordersPopItem
     *
     * @param \OrderBundle\Entity\OrdersPopItem $ordersPopItem
     */
    public function removeOrdersPopItem(\OrderBundle\Entity\OrdersPopItem $ordersPopItem)
    {
        $this->orders_pop_item->removeElement($ordersPopItem);
    }

    /**
     * Add warehousePopInventory
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventory $warehousePopInventory
     *
     * @return PopItem
     */
    public function addWarehousePopInventory(\WarehouseBundle\Entity\WarehousePopInventory $warehousePopInventory)
    {
        $this->warehouse_pop_inventory[] = $warehousePopInventory;

        return $this;
    }

    /**
     * Remove warehousePopInventory
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventory $warehousePopInventory
     */
    public function removeWarehousePopInventory(\WarehouseBundle\Entity\WarehousePopInventory $warehousePopInventory)
    {
        $this->warehouse_pop_inventory->removeElement($warehousePopInventory);
    }

    /**
     * Add warehousePopInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventoryOnHold $warehousePopInventoryOnHold
     *
     * @return PopItem
     */
    public function addWarehousePopInventoryOnHold(\WarehouseBundle\Entity\WarehousePopInventoryOnHold $warehousePopInventoryOnHold)
    {
        $this->warehouse_pop_inventory_on_hold[] = $warehousePopInventoryOnHold;

        return $this;
    }

    /**
     * Remove warehousePopInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventoryOnHold $warehousePopInventoryOnHold
     */
    public function removeWarehousePopInventoryOnHold(\WarehouseBundle\Entity\WarehousePopInventoryOnHold $warehousePopInventoryOnHold)
    {
        $this->warehouse_pop_inventory_on_hold->removeElement($warehousePopInventoryOnHold);
    }

    /**
     * Set channel
     *
     * @param \InventoryBundle\Entity\Channel $channel
     *
     * @return PopItem
     */
    public function setChannel(\InventoryBundle\Entity\Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return \InventoryBundle\Entity\Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Get promoKitAvailable
     *
     * @return boolean
     */
    public function getPromoKitAvailable()
    {
        return $this->promo_kit_available;
    }

    /**
     * Set hideOrder
     *
     * @param \bool $hideOrder
     *
     * @return PopItem
     */
    public function setHideOrder($hideOrder)
    {
        $this->hide_order = $hideOrder;

        return $this;
    }

    /**
     * Get hideOrder
     *
     * @return \bool
     */
    public function getHideOrder()
    {
        return $this->hide_order;
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
     * Get promoKitAvailable
     *
     * @return boolean
     */
    public function getPromoKitAvailable()
    {
        return $this->promo_kit_available;
    }

    /**
     * Add promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     *
     * @return PopItem
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

    /**
     * @return ArrayCollection
     */
    public function getWarehouses()
    {
        return $this->warehouses;
    }

    /**
     * @param mixed $warehouses
     */
    public function setWarehouses($warehouses)
    {
        $this->warehouses = $warehouses;
    }

    /**
     * Get isHideOnOrder
     *
     * @return boolean
     */
    public function getIsHideOnOrder()
    {
        return $this->is_hide_on_order;
    }

    /**
     * Add warehouse
     *
     * @param \WarehouseBundle\Entity\Warehouse $warehouse
     *
     * @return PopItem
     */
    public function addWarehouse(\WarehouseBundle\Entity\Warehouse $warehouse)
    {
        $this->warehouses[] = $warehouse;

        return $this;
    }

    /**
     * Remove warehouse
     *
     * @param \WarehouseBundle\Entity\Warehouse $warehouse
     */
    public function removeWarehouse(\WarehouseBundle\Entity\Warehouse $warehouse)
    {
        $this->warehouses->removeElement($warehouse);
    }
}
