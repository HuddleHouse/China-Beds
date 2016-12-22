<?php

namespace WarehouseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\OrdersProductVariant;
use OrderBundle\Entity\OrdersWarehouseInfo;

/**
 * Warehouse
 *
 * @ORM\Table(name="warehouses")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\WarehouseRepository")
 */
class Warehouse
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="bigint", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address_1", type="string", length=255, nullable=true)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address_2", type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="list_id", type="string", length=255, nullable=true)
     */
    private $list_id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=255, nullable=true)
     */
    private $contact;
    
    /**
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_name", type="string", length=255, nullable=true)
     */
    private $manager_name;

    /**
     * @var string
     *
     * @ORM\Column(name="management_comp", type="string", length=255, nullable=true)
     */
    private $management_comp;

    /**
     * @var string
     *
     * @ORM\Column(name="email_2", type="string", length=255, nullable=true)
     */
    private $email_2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $shipping_label_image_type = 'PNG';

    /**
     * @var string
     *
     * @ORM\Column(name="container_number", length=255, nullable=true)
     */
    private $containerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", length=255, nullable=true)
     */
    private $orderNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="managed_warehouses")
     */
    protected $managers;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="warehouses")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="warehouse_1")
     */
    protected $users_1;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="warehouse_2")
     */
    protected $users_2;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="warehouse_3")
     */
    protected $users_3;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehouseInventory", mappedBy="warehouse")
     */
    private $inventory;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehouseInventoryOnHold", mappedBy="warehouse")
     */
    private $inventory_on_hold;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehousePopInventory", mappedBy="warehouse")
     */
    private $pop_inventory;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\WarehousePopInventoryOnHold", mappedBy="warehouse")
     */
    private $pop_inventory_on_hold;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\PurchaseOrder", mappedBy="warehouse")
     */
    private $purchase_orders;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockTransfer", mappedBy="receiving_warehouse")
     */
    private $stock_transfer_receiving;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockTransfer", mappedBy="departing_warehouse")
     */
    private $stock_transfer_departing;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockAdjustment", mappedBy="warehouse")
     */
    private $stock_adjustments;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersWarehouseInfo", mappedBy="warehouse")
     */
    private $orders_warehouse_info;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="warehouses")
     */
    private $channel;

    /**
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\PopItem", mappedBy="warehouses")
     */
    private $pop_items;

    public function __construct() {
        $this->purchase_orders = new ArrayCollection();
        $this->stock_adjustments = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->pop_items = new ArrayCollection();
        $this->users_1 = new ArrayCollection();
        $this->users_2 = new ArrayCollection();
        $this->pop_inventory = new ArrayCollection();
        $this->pop_inventory_on_hold = new ArrayCollection();
        $this->inventory_on_hold = new ArrayCollection();
        $this->users_3 = new ArrayCollection();
        $this->stock_transfer_departing = new ArrayCollection();
        $this->stock_transfer_receiving = new ArrayCollection();
        $this->inventory = new ArrayCollection();
        $this->orders_warehouse_info = new ArrayCollection();
    }

    public function getChannelNamesArray() {
        return [
            $this->getChannel()->getName()
        ];
    }

    public function getChannelsArray() {
        return [ $this->getChannel()->getId() => [
                    'id' => $this->getChannel()->getId(),
                    'name' => $this->getChannel()->getName(),
                    'url' => $this->getChannel()->getUrl(),
                ]
            ];
    }

    public function addChannel($channel)
    {
        $this->setChannel($channel);
    }

    /**
     * Remove channels
     *
     * @param \InventoryBundle\Entity\Channel $channel
     */
    public function removeChannel($channel)
    {
        $this->removeChannel($channel);
    }

    /**
     * Get channels
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChannels()
    {
        return [ $this->getChannel() ];
    }

    public function belongsToChannel(Channel $channel) {
        return $this->getChannel() == $channel;
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
     * @return Warehouse
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
     * Set phone
     *
     * @param integer $phone
     *
     * @return Warehouse
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Warehouse
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return Warehouse
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return Warehouse
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Warehouse
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param integer $zip
     *
     * @return Warehouse
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getUsers1()
    {
        return $this->users_1;
    }

    /**
     * @param mixed $users_1
     */
    public function setUsers1($users_1)
    {
        $this->users_1 = $users_1;
    }

    /**
     * @return mixed
     */
    public function getUsers2()
    {
        return $this->users_2;
    }

    /**
     * @param mixed $users_2
     */
    public function setUsers2($users_2)
    {
        $this->users_2 = $users_2;
    }

    /**
     * @return mixed
     */
    public function getUsers3()
    {
        return $this->users_3;
    }

    /**
     * @param mixed $users_3
     */
    public function setUsers3($users_3)
    {
        $this->users_3 = $users_3;
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return mixed
     */
    public function getPurchaseOrders()
    {
        return $this->purchase_orders;
    }

    /**
     * @param mixed $purchase_orders
     */
    public function setPurchaseOrders($purchase_orders)
    {
        $this->purchase_orders = $purchase_orders;
    }

    /**
     * @return mixed
     */
    public function getStockTransferreceiving()
    {
        return $this->stock_transfer_receiving;
    }

    /**
     * @param mixed $stock_transfer_receiving
     */
    public function setStockTransferreceiving($stock_transfer_receiving)
    {
        $this->stock_transfer_receiving = $stock_transfer_receiving;
    }

    /**
     * @return mixed
     */
    public function getStockAdjustments()
    {
        return $this->stock_adjustments;
    }

    /**
     * @param mixed $stock_adjustments
     */
    public function setStockAdjustments($stock_adjustments)
    {
        $this->stock_adjustments = $stock_adjustments;
    }

    /**
     * @return mixed
     */
    public function getStockTransferDeparting()
    {
        return $this->stock_transfer_departing;
    }

    /**
     * @param mixed $stock_transfer_departing
     */
    public function setStockTransferDeparting($stock_transfer_departing)
    {
        $this->stock_transfer_departing = $stock_transfer_departing;
    }

    /**
     * @return mixed
     */
    public function getOrdersWarehouseInfo()
    {
        return $this->orders_warehouse_info;
    }

    /**
     * @param mixed $orders_warehouse_info
     */
    public function setOrdersWarehouseInfo($orders_warehouse_info)
    {
        $this->orders_warehouse_info = $orders_warehouse_info;
    }
    

    /**
     * Set managerName
     *
     * @param string $managerName
     *
     * @return Warehouse
     */
    public function setManagerName($managerName)
    {
        $this->manager_name = $managerName;

        return $this;
    }

    /**
     * Get managerName
     *
     * @return string
     */
    public function getManagerName()
    {
        return $this->manager_name;
    }

    /**
     * Set managementComp
     *
     * @param string $managementComp
     *
     * @return Warehouse
     */
    public function setManagementComp($managementComp)
    {
        $this->management_comp = $managementComp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @param mixed $managers
     */
    public function setManagers($managers)
    {
        $this->managers = $managers;
    }

    /**
     * Get managementComp
     *
     * @return string
     */
    public function getManagementComp()
    {
        return $this->management_comp;
    }

    /**
     * Set email2
     *
     * @param string $email2
     *
     * @return Warehouse
     */
    public function setEmail2($email2)
    {
        $this->email_2 = $email2;

        return $this;
    }

    /**
     * Get email2
     *
     * @return string
     */
    public function getEmail2()
    {
        return $this->email_2;
    }

    /**
     * Add users1
     *
     * @param \AppBundle\Entity\User $users1
     *
     * @return Warehouse
     */
    public function addUsers1(\AppBundle\Entity\User $users1)
    {
        $this->users_1[] = $users1;

        return $this;
    }

    /**
     * Remove users1
     *
     * @param \AppBundle\Entity\User $users1
     */
    public function removeUsers1(\AppBundle\Entity\User $users1)
    {
        $this->users_1->removeElement($users1);
    }

    /**
     * Add users2
     *
     * @param \AppBundle\Entity\User $users2
     *
     * @return Warehouse
     */
    public function addUsers2(\AppBundle\Entity\User $users2)
    {
        $this->users_2[] = $users2;

        return $this;
    }

    /**
     * Remove users2
     *
     * @param \AppBundle\Entity\User $users2
     */
    public function removeUsers2(\AppBundle\Entity\User $users2)
    {
        $this->users_2->removeElement($users2);
    }

    /**
     * Add users3
     *
     * @param \AppBundle\Entity\User $users3
     *
     * @return Warehouse
     */
    public function addUsers3(\AppBundle\Entity\User $users3)
    {
        $this->users_3[] = $users3;

        return $this;
    }

    /**
     * Remove users3
     *
     * @param \AppBundle\Entity\User $users3
     */
    public function removeUsers3(\AppBundle\Entity\User $users3)
    {
        $this->users_3->removeElement($users3);
    }

    /**
     * Add inventory
     *
     * @param \WarehouseBundle\Entity\WarehouseInventory $inventory
     *
     * @return Warehouse
     */
    public function addInventory(\WarehouseBundle\Entity\WarehouseInventory $inventory)
    {
        $this->inventory[] = $inventory;

        return $this;
    }

    /**
     * Remove inventory
     *
     * @param \WarehouseBundle\Entity\WarehouseInventory $inventory
     */
    public function removeInventory(\WarehouseBundle\Entity\WarehouseInventory $inventory)
    {
        $this->inventory->removeElement($inventory);
    }

    /**
     * Add purchaseOrder
     *
     * @param \WarehouseBundle\Entity\PurchaseOrder $purchaseOrder
     *
     * @return Warehouse
     */
    public function addPurchaseOrder(\WarehouseBundle\Entity\PurchaseOrder $purchaseOrder)
    {
        $this->purchase_orders[] = $purchaseOrder;

        return $this;
    }

    /**
     * Remove purchaseOrder
     *
     * @param \WarehouseBundle\Entity\PurchaseOrder $purchaseOrder
     */
    public function removePurchaseOrder(\WarehouseBundle\Entity\PurchaseOrder $purchaseOrder)
    {
        $this->purchase_orders->removeElement($purchaseOrder);
    }

    /**
     * Add stockTransferReceiving
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransferReceiving
     *
     * @return Warehouse
     */
    public function addStockTransferReceiving(\WarehouseBundle\Entity\StockTransfer $stockTransferReceiving)
    {
        $this->stock_transfer_receiving[] = $stockTransferReceiving;

        return $this;
    }

    /**
     * Remove stockTransferReceiving
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransferReceiving
     */
    public function removeStockTransferReceiving(\WarehouseBundle\Entity\StockTransfer $stockTransferReceiving)
    {
        $this->stock_transfer_receiving->removeElement($stockTransferReceiving);
    }

    /**
     * Add stockTransferDeparting
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransferDeparting
     *
     * @return Warehouse
     */
    public function addStockTransferDeparting(\WarehouseBundle\Entity\StockTransfer $stockTransferDeparting)
    {
        $this->stock_transfer_departing[] = $stockTransferDeparting;

        return $this;
    }

    /**
     * Remove stockTransferDeparting
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransferDeparting
     */
    public function removeStockTransferDeparting(\WarehouseBundle\Entity\StockTransfer $stockTransferDeparting)
    {
        $this->stock_transfer_departing->removeElement($stockTransferDeparting);
    }

    /**
     * Add stockAdjustment
     *
     * @param \WarehouseBundle\Entity\StockAdjustment $stockAdjustment
     *
     * @return Warehouse
     */
    public function addStockAdjustment(\WarehouseBundle\Entity\StockAdjustment $stockAdjustment)
    {
        $this->stock_adjustments[] = $stockAdjustment;

        return $this;
    }

    /**
     * Remove stockAdjustment
     *
     * @param \WarehouseBundle\Entity\StockAdjustment $stockAdjustment
     */
    public function removeStockAdjustment(\WarehouseBundle\Entity\StockAdjustment $stockAdjustment)
    {
        $this->stock_adjustments->removeElement($stockAdjustment);
    }

    /**
     * Add ordersWarehouseInfo
     *
     * @param \OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo
     *
     * @return Warehouse
     */
    public function addOrdersWarehouseInfo(\OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo)
    {
        $this->orders_warehouse_info[] = $ordersWarehouseInfo;

        return $this;
    }

    /**
     * Remove ordersWarehouseInfo
     *
     * @param \OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo
     */
    public function removeOrdersWarehouseInfo(\OrderBundle\Entity\OrdersWarehouseInfo $ordersWarehouseInfo)
    {
        $this->orders_warehouse_info->removeElement($ordersWarehouseInfo);
    }

    /**
     * @return mixed
     */
    public function getInventoryOnHold()
    {
        return $this->inventory_on_hold;
    }

    /**
     * @param mixed $inventory_on_hold
     */
    public function setInventoryOnHold($inventory_on_hold)
    {
        $this->inventory_on_hold = $inventory_on_hold;
    }

    /**
     * @return mixed
     */
    public function getPopInventory()
    {
        return $this->pop_inventory;
    }

    /**
     * @param mixed $pop_inventory
     */
    public function setPopInventory($pop_inventory)
    {
        $this->pop_inventory = $pop_inventory;
    }

    /**
     * @return mixed
     */
    public function getPopInventoryOnHold()
    {
        return $this->pop_inventory_on_hold;
    }

    /**
     * @param mixed $pop_inventory_on_hold
     */
    public function setPopInventoryOnHold($pop_inventory_on_hold)
    {
        $this->pop_inventory_on_hold = $pop_inventory_on_hold;
    }

    /**
     * @return mixed
     */
    public function getShippingLabelImageType()
    {
        return $this->shipping_label_image_type;
    }

    /**
     * @param mixed $shipping_label_image_type
     */
    public function setShippingLabelImageType($shipping_label_image_type)
    {
        $this->shipping_label_image_type = $shipping_label_image_type;
    }

    /**
     * Set containerNumber
     *
     * @param string $containerNumber
     *
     * @return Warehouse
     */
    public function setContainerNumber($containerNumber)
    {
        $this->containerNumber = $containerNumber;

        return $this;
    }

    /**
     * Get containerNumber
     *
     * @return string
     */
    public function getContainerNumber()
    {
        return $this->containerNumber;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return Warehouse
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
     * Add manager
     *
     * @param \AppBundle\Entity\User $manager
     *
     * @return Warehouse
     */
    public function addManager(\AppBundle\Entity\User $manager)
    {
        $this->managers[] = $manager;

        return $this;
    }

    /**
     * Remove manager
     *
     * @param \AppBundle\Entity\User $manager
     */
    public function removeManager(\AppBundle\Entity\User $manager)
    {
        $this->managers->removeElement($manager);
    }

    /**
     * Add inventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehouseInventoryOnHold $inventoryOnHold
     *
     * @return Warehouse
     */
    public function addInventoryOnHold(\WarehouseBundle\Entity\WarehouseInventoryOnHold $inventoryOnHold)
    {
        $this->inventory_on_hold[] = $inventoryOnHold;

        return $this;
    }

    /**
     * Remove inventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehouseInventoryOnHold $inventoryOnHold
     */
    public function removeInventoryOnHold(\WarehouseBundle\Entity\WarehouseInventoryOnHold $inventoryOnHold)
    {
        $this->inventory_on_hold->removeElement($inventoryOnHold);
    }

    /**
     * Add popInventory
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventory $popInventory
     *
     * @return Warehouse
     */
    public function addPopInventory(\WarehouseBundle\Entity\WarehousePopInventory $popInventory)
    {
        $this->pop_inventory[] = $popInventory;

        return $this;
    }

    /**
     * Remove popInventory
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventory $popInventory
     */
    public function removePopInventory(\WarehouseBundle\Entity\WarehousePopInventory $popInventory)
    {
        $this->pop_inventory->removeElement($popInventory);
    }

    /**
     * Add popInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventoryOnHold $popInventoryOnHold
     *
     * @return Warehouse
     */
    public function addPopInventoryOnHold(\WarehouseBundle\Entity\WarehousePopInventoryOnHold $popInventoryOnHold)
    {
        $this->pop_inventory_on_hold[] = $popInventoryOnHold;

        return $this;
    }

    /**
     * Remove popInventoryOnHold
     *
     * @param \WarehouseBundle\Entity\WarehousePopInventoryOnHold $popInventoryOnHold
     */
    public function removePopInventoryOnHold(\WarehouseBundle\Entity\WarehousePopInventoryOnHold $popInventoryOnHold)
    {
        $this->pop_inventory_on_hold->removeElement($popInventoryOnHold);
    }

    /**
     * @return ArrayCollection
     */
    public function getPopItems()
    {
        return $this->pop_items;
    }

    /**
     * @param mixed $pop_items
     */
    public function setPopItems($pop_items)
    {
        $this->pop_items = $pop_items;
    }

    /**
     * Set channel
     *
     * @param \InventoryBundle\Entity\Channel $channel
     *
     * @return Warehouse
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
     * Add popItem
     *
     * @param \InventoryBundle\Entity\PopItem $popItem
     *
     * @return Warehouse
     */
    public function addPopItem(\InventoryBundle\Entity\PopItem $popItem)
    {
        $this->pop_items[] = $popItem;

        return $this;
    }

    /**
     * Remove popItem
     *
     * @param \InventoryBundle\Entity\PopItem $popItem
     */
    public function removePopItem(\InventoryBundle\Entity\PopItem $popItem)
    {
        $this->pop_items->removeElement($popItem);
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    public function toArray() {
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
        ];
    }
}
