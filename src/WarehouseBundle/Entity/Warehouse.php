<?php

namespace WarehouseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="phone", type="integer", nullable=true)
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
     * @ORM\Column(name="address_1", type="string", length=255)
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
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="list_id", type="string", length=255)
     */
    private $list_id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=255)
     */
    private $contact;
    
    /**
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

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
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\Channel")
     * @ORM\JoinTable(name="warehouse_channels")
     */
    private $channels;

    public function __construct() {
        $this->purchase_orders = new ArrayCollection();
        $this->stock_adjustments = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->users_1 = new ArrayCollection();
        $this->users_2 = new ArrayCollection();
        $this->users_3 = new ArrayCollection();
        $this->stock_transfer_departing = new ArrayCollection();
        $this->stock_transfer_receiving = new ArrayCollection();
        $this->inventory = new ArrayCollection();
        $this->orders_warehouse_info = new ArrayCollection();
    }

    public function getChannelIdsArray() {
        $data = array();
        foreach($this->channels as $channel)
            $data[] = $channel->getId();
        return $data;
    }

    public function getChannelsArray() {
        $data = array();
        foreach($this->channels as $channel)
            $data[$channel->getId()] = array(
                'id' => $channel->getId(),
                'name' => $channel->getName(),
                'url' => $channel->getUrl()
            );
        return $data;
    }

    public function addChannel($channel)
    {
        if(!$this->channels->contains($channel))
            $this->channels[] = $channel;

        return $this;
    }

    /**
     * Remove channels
     *
     * @param \InventoryBundle\Entity\Channel $channel
     */
    public function removeChannel($channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * Get channels
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChannels()
    {
        return $this->channels;
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
    
}

