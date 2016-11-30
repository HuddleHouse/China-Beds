<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Orders;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use WarehouseBundle\Entity\Warehouse;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $address_1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $address_2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $company_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $distributor_fedex_number;

    /**
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_routing_number", type="integer", length=9, nullable=true)
     */
    private $ach_routing_number;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_account_number", type="string", length=17, nullable=true)
     */
    private $ach_account_number;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_residential = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_show_credit = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_show_warranty = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_volume_discount = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_charge_shipping = true;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent_retail_kit = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_current_retailer = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_online_intentions = false;

    /**
     * @ORM\Column(name="hide_rebate", type="boolean")
     */
    protected $hideRebate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $online_web_url;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="role_users",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\PriceGroup", inversedBy="users")
     * @ORM\JoinTable(name="price_group_users")
     */
    private $price_groups;

    /**
     * @ORM\ManyToMany(targetEntity="InventoryBundle\Entity\Channel", inversedBy="users")
     * @ORM\JoinTable(name="user_channels")
     */
    private $user_channels;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Office", inversedBy="users")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\RebateSubmission", mappedBy="submittedForUser")
     */
    private $rebate_submissions;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\RebateSubmission", mappedBy="submittedByUser")
     */
    private $submitted_rebates;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="submittedForUser")
     */
    private $warranty_claims;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="submittedByUser")
     */
    private $submitted_warranty_claims;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\PurchaseOrder", mappedBy="user")
     */
    private $purchase_orders;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Orders", mappedBy="submitted_for_user")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Orders", mappedBy="submitted_by_user")
     */
    private $submitted_orders;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockTransfer", mappedBy="user")
     */
    private $stock_transfers;

    /**
     * @ORM\ManyToMany(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="users")
     * @ORM\JoinTable(name="user_warehouses",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")}
     * )
     */
    protected $warehouses;

    /**
     * @ORM\ManyToMany(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="managers")
     * @ORM\JoinTable(name="user_managed_warehouses",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")}
     * )
     */
    protected $managed_warehouses;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\StockAdjustment", mappedBy="user")
     */
    private $stock_adjustments;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="users_1")
     * @ORM\JoinColumn(name="warehouse_1", referencedColumnName="id")
     */
    private $warehouse_1;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="users_2")
     * @ORM\JoinColumn(name="warehouse_2", referencedColumnName="id")
     */
    private $warehouse_2;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Warehouse", inversedBy="users_3")
     * @ORM\JoinColumn(name="warehouse_3", referencedColumnName="id")
     */
    private $warehouse_3;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="my_distributor", cascade={"all"})
     */
    private $retailers;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="retailers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="my_distributor_id", referencedColumnName="id")
     */
    private $my_distributor;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="my_sales_rep", cascade={"all"})
     */
    private $distributors;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="distributors", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="my_sales_rep_id", referencedColumnName="id")
     */
    private $my_sales_rep;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="my_sales_manager", cascade={"all"})
     */
    private $sales_reps;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="sales_reps", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="my_sales_manager_id", referencedColumnName="id")
     */
    private $my_sales_manager;

    /**
     * @OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="submittedForUser")
     */
    private $ledgers;

    /**
     * @OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="submittedByUser")
     */
    private $submitted_ledgers;

    /**
     * @OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="creditedByUser")
     */
    private $credited_ledgers;

    private $active_channel = null;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\PromoKitOrders", mappedBy="submittedByUser")
     */
    private $promo_kit_orders;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\CreditRequest", mappedBy="submittedForUser", cascade={"remove"})
     */
    protected $creditRequestFor;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\CreditRequest", mappedBy="submittedByUser", cascade={"remove"})
     */
    protected $creditRequestBy;




    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->warranty_claims = new ArrayCollection();
        $this->managed_warehouses = new ArrayCollection();
        $this->submitted_warranty_claims = new ArrayCollection();
        $this->user_channels = new ArrayCollection();
        $this->rebate_submissions = new ArrayCollection();
        $this->submitted_rebates = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->price_groups = new ArrayCollection();
        $this->purchase_orders = new ArrayCollection();
        $this->stock_adjustments = new ArrayCollection();
        $this->retailers = new ArrayCollection();
        $this->distributors = new ArrayCollection();
        $this->sales_reps = new ArrayCollection();
        $this->ledgers = new ArrayCollection();
        $this->submitted_ledgers = new ArrayCollection();
        $this->credited_ledgers = new ArrayCollection();
        $this->promo_kit_orders = new ArrayCollection();
        $this->warehouses = new ArrayCollection();
        $this->creditRequestFor = new ArrayCollection();
        $this->creditRequestBy = new ArrayCollection();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
//        if($this->hasRole('ROLE_RETAILER')) {
//            $count = 0;
//            foreach($this->getUserChannels() as $channel)
//                $count++;
//            if ($count > 1) {
//                $context->buildViolation('A retailer can only be on one Channel.')
//                    ->atPath('user_channels')
//                    ->addViolation();
//            }
//        }
    }



    public function getFullName() {
        return $this->first_name . " " . $this->last_name;
    }

    public function hasLedger() {
        $count = 0;
        foreach($this->ledgers as $ledger) {
            $count++;
            break;
        }
        if($count == 0)
            return false;
        else
            return true;
    }

    public function hasPendingOrders() {
        $count = 0;
        foreach($this->orders as $order) {
            if(in_array($order->getStatus()->getName(), ['Pending', 'Draft'])) {
                $count++;
                break;
            }
        }
        if($count == 0)
            return false;
        else
            return true;
    }

    public function getOpenBalanceTotal($channel_id = null) {
        $total = 0;
        foreach($this->orders as $order) {
            if(in_array($order->getStatus()->getName(), [Orders::STATUS_READY_TO_SHIP, Orders::STATUS_SHIPPED])) {
                if ($channel_id == null) {
                    $total += $order->getBalance();
                } elseif ($order->getChannel()->getId() == $channel_id) {
                    $total += $order->getBalance();
                }
            }
        }
        return $total;
    }

    public function getPendingOrderTotal($channel_id = null) {
        $total = 0;
        foreach($this->orders as $order) {
            if(in_array($order->getStatus()->getName(), [Orders::STATUS_PENDING, Orders::STATUS_DRAFT])) {
                if($channel_id == null)
                    $total += $order->getBalance();
                else if($order->getChannel()->getId() == $channel_id)
                    $total += $order->getBalance();
            }
        }
        return $total;
    }

    public function getLedgerTotal($channel_id = null) {
        $total = 0;
        foreach($this->ledgers as $ledger) {
            if($channel_id == null && $ledger->getChannel()->getId() == $this->getActiveChannel()->getId())
                $total += $ledger->getAmountCredited();
            else if($ledger->getChannel()->getId() == $channel_id)
                $total += $ledger->getAmountCredited();
        }
        return $total;
    }

    public function getRouteNames() {
        $data = array();
        if(isset($this->groups)) {
            foreach($this->groups as $role) {
                foreach($role->getPermissions() as $permission) {
                    $data[$permission->getRouteName()] = $permission->getRouteName();
                }
            }
        }

        return $data;
    }

    public function getUserChannelsArray() {
        $data = array();
        foreach($this->user_channels as $channel)
            $data[$channel->getId()] = array(
                'id' => $channel->getId(),
                'name' => $channel->getName(),
                'url' => $channel->getUrl()
            );
        return $data;
    }

    public function getPriceGroupsString() {
        //get user price groups and format string for SQL query
        $user_price_groups = "";
        foreach($this->price_groups as $group)
            $user_price_groups .= $group->getId(). ", ";

        $user_price_groups = substr($user_price_groups, 0, -2);
        return $user_price_groups;
    }

    public function getGroupsArray() {
        //get user price groups and format string for SQL query
        $user_groups = array();
        foreach($this->groups as $group)
            $user_groups[$group->getName()] = $group->getId(). ", ";

        return $user_groups;
    }

    public function canManageWarehouse(Warehouse $warehouse){
        foreach($this->managed_warehouses as $ware) {
            if($ware->getName() == $warehouse->getName())
                return true;
        }
        return false;
    }
    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->address_1;
    }

    /**
     * @param mixed $address_1
     */
    public function setAddress1($address_1)
    {
        $this->address_1 = $address_1;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->address_2;
    }

    /**
     * @param mixed $address_2
     */
    public function setAddress2($address_2)
    {
        $this->address_2 = $address_2;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = preg_replace("/[^0-9,.]/", "", $phone);
    }

    /**
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param int $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getIsResidential()
    {
        return $this->is_residential;
    }

    /**
     * @return mixed
     */
    public function getManagedWarehouses()
    {
        return $this->managed_warehouses;
    }

    /**
     * @param mixed $managed_warehouses
     */
    public function setManagedWarehouses($managed_warehouses)
    {
        $this->managed_warehouses = $managed_warehouses;
    }



    /**
     * @param mixed $is_residential
     */
    public function setIsResidential($is_residential)
    {
        $this->is_residential = $is_residential;
    }

    /**
     * @return mixed
     */
    public function getIsShowCredit()
    {
        return $this->is_show_credit;
    }

    /**
     * @param mixed $is_show_credit
     */
    public function setIsShowCredit($is_show_credit)
    {
        $this->is_show_credit = $is_show_credit;
    }

    /**
     * @return mixed
     */
    public function getIsShowWarranty()
    {
        return $this->is_show_warranty;
    }

    /**
     * @param mixed $is_show_warranty
     */
    public function setIsShowWarranty($is_show_warranty)
    {
        $this->is_show_warranty = $is_show_warranty;
    }

    /**
     * @return mixed
     */
    public function getIsVolumeDiscount()
    {
        return $this->is_volume_discount;
    }

    /**
     * @param mixed $is_volume_discount
     */
    public function setIsVolumeDiscount($is_volume_discount)
    {
        $this->is_volume_discount = $is_volume_discount;
    }

    /**
     * @return mixed
     */
    public function getIsChargeShipping()
    {
        return $this->is_charge_shipping;
    }

    /**
     * @param mixed $is_charge_shipping
     */
    public function setIsChargeShipping($is_charge_shipping)
    {
        $this->is_charge_shipping = $is_charge_shipping;
    }

    /**
     * @return mixed
     */
    public function getSentRetailKit()
    {
        return $this->sent_retail_kit;
    }

    /**
     * @param mixed $sent_retail_kit
     */
    public function setSentRetailKit($sent_retail_kit)
    {
        $this->sent_retail_kit = $sent_retail_kit;
    }

    /**
     * @return mixed
     */
    public function getIsOnlineIntentions()
    {
        return $this->is_online_intentions;
    }

    /**
     * @param mixed $is_online_intentions
     */
    public function setIsOnlineIntentions($is_online_intentions)
    {
        $this->is_online_intentions = $is_online_intentions;
    }

    public function getGroups()
    {
        return $this->groups;
    }


    public function addGroup(GroupInterface $group)
    {
        return parent::addGroup($group);
    }


    public function addRole($role)
    {
        $role = strtoupper($role['roles']);
        if($role === static::ROLE_DEFAULT) {
            return $this;
        }
    }

    public function addPriceGroup(\AppBundle\Entity\PriceGroup $priceGroup)
    {
        if(!$this->price_groups->contains($priceGroup))
            $this->price_groups[] = $priceGroup;

        return $this;
    }

    /**
     * Remove payTypes
     *
     * @param PriceGroup $priceGroup
     */
    public function removePriceGroup(PriceGroup $priceGroup)
    {
        $this->price_groups->removeElement($priceGroup);
    }

    /**
     * Get payTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPriceGroups()
    {
        return $this->price_groups;
    }

    public function addUserChannel($userChannel)
    {
        if(!$this->user_channels->contains($userChannel))
            $this->user_channels[] = $userChannel;

        return $this;
    }

    /**
     * Remove payTypes
     *
     * @param $userChannel
     */
    public function removeUserChannel($userChannel)
    {
        $this->user_channels->removeElement($userChannel);
    }

    /**
     * Get payTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserChannels()
    {
        return $this->user_channels;
    }

    /**
     * @return mixed
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     */
    public function setOffice($office)
    {
        $this->office = $office;
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
     * @return ArrayCollection
     */
    public function getSubmittedWarrantyClaims()
    {
        return $this->submitted_warranty_claims;
    }

    /**
     * @param ArrayCollection $submitted_warranty_claims
     */
    public function setSubmittedWarrantyClaims($submitted_warranty_claims)
    {
        $this->submitted_warranty_claims = $submitted_warranty_claims;
    }

    public function getName() {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    /**
     * @return mixed
     */
    public function getWarehouse1()
    {
        return $this->warehouse_1;
    }

    /**
     * @param mixed $warehouse_1
     */
    public function setWarehouse1($warehouse_1)
    {
        $this->warehouse_1 = $warehouse_1;
    }

    /**
     * @return mixed
     */
    public function getWarehouse2()
    {
        return $this->warehouse_2;
    }

    /**
     * @param mixed $warehouse_2
     */
    public function setWarehouse2($warehouse_2)
    {
        $this->warehouse_2 = $warehouse_2;
    }

    /**
     * @return mixed
     */
    public function getWarehouse3()
    {
        return $this->warehouse_3;
    }

    /**
     * @param mixed $warehouse_3
     */
    public function setWarehouse3($warehouse_3)
    {
        $this->warehouse_3 = $warehouse_3;
    }

    public function getWarehouses(Channel $channel = null) {
        if ( $this->getWarehouse1() && !$this->warehouses->contains($this->getWarehouse1()) && ($channel == null || $channel->getId() == $this->getWarehouse1()->getChannel()->getId()) ) {
            $this->warehouses->add($this->getWarehouse1());
        }
        if ( $this->getWarehouse2() && !$this->warehouses->contains($this->getWarehouse2()) && ($channel == null || $channel->getId() == $this->getWarehouse2()->getChannel()->getId()) ) {
            $this->warehouses->add($this->getWarehouse2());
        }
        if ( $this->getWarehouse3() && !$this->warehouses->contains($this->getWarehouse3()) && ($channel == null || $channel->getId() == $this->getWarehouse3()->getChannel()->getId()) ) {
            $this->warehouses->add($this->getWarehouse3());
        }
        return $this->warehouses;
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
    public function getStockTransfers()
    {
        return $this->stock_transfers;
    }

    /**
     * @param mixed $stock_transfers
     */
    public function setStockTransfers($stock_transfers)
    {
        $this->stock_transfers = $stock_transfers;
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
    public function getIsCurrentRetailer()
    {
        return $this->is_current_retailer;
    }

    /**
     * @param mixed $is_current_retailer
     */
    public function setIsCurrentRetailer($is_current_retailer)
    {
        $this->is_current_retailer = $is_current_retailer;
    }

    /**
     * @return mixed
     */
    public function getOnlineWebUrl()
    {
        return $this->online_web_url;
    }

    /**
     * @param mixed $online_web_url
     */
    public function setOnlineWebUrl($online_web_url)
    {
        $this->online_web_url = $online_web_url;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * @param mixed $company_name
     */
    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
    }

    /**
     * @return mixed
     */
    public function getRetailers()
    {
        $data = array();
        foreach($this->retailers as $retailer)
            if($retailer->hasRole('ROLE_RETAILER'))
                if ( $retailer->belongsToChannel($this->getActiveChannel() ? $this->getActiveChannel() : $this->getUserChannels()->toArray()) )
                    $data[] = $retailer;
        return $data;
    }

    /**
     * @param mixed $retailers
     */
    public function setRetailers($retailers)
    {
        $this->retailers = $retailers;
    }

    public function addRetailer(User $retailer) {
        if(!$this->retailers->contains($retailer))
            $this->retailers[] = $retailer;
    }

    public function removeRetailer($retailer) {
        $this->retailers->removeElement($retailer);
    }

    /**
     * @return mixed
     */
    public function getMyDistributor()
    {
        return $this->my_distributor;
    }

    /**
     * @param mixed $my_distributor
     */
    public function setMyDistributor($my_distributor)
    {
        $this->my_distributor = $my_distributor;
    }

    /**
     * @return mixed
     */
    public function getDistributors()
    {
        $data = array();
        foreach($this->distributors as $distributor)
            if($distributor->hasRole('ROLE_DISTRIBUTOR'))
                if ( $distributor->belongsToChannel($this->getActiveChannel() ? $this->getActiveChannel() : $this->getUserChannels()->toArray()) )
                    $data[] = $distributor;
        return $data;
    }

    /**
     * @param mixed $distributors
     */
    public function setDistributors($distributors)
    {
        $this->distributors = $distributors;
    }

    public function addDistributor(User $distributor) {
        if(!$this->distributors->contains($distributor))
            $this->distributors[] = $distributor;
    }

    public function removeDistributor($distributor)
    {
        $this->distributors->removeElement($distributor);
    }

    /**
     * @return mixed
     */
    public function getMySalesRep()
    {
        return $this->my_sales_rep;
    }

    /**
     * @param mixed $my_sales_rep
     */
    public function setMySalesRep($my_sales_rep)
    {
        $this->my_sales_rep = $my_sales_rep;
    }

    /**
     * @return mixed
     */
    public function getSalesReps()
    {
        $data = array();
        foreach($this->sales_reps as $rep)
            if($rep->hasRole('ROLE_SALES_REP'))
                if ( $rep->belongsToChannel($this->getActiveChannel() ? $this->getActiveChannel() : $this->getUserChannels()->toArray()) )
                    $data[] = $rep;
        return $data;
    }

    public function belongsToChannel($channel = null) {
        if ( !is_array($channel) ) { $channel = [$channel]; }

        foreach($this->getUserChannels() as $user_channel) {
            foreach($channel as $chan) {
                if ($user_channel->getId() == $chan->getId()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param mixed $sales_reps
     */
    public function setSalesReps($sales_reps)
    {
        $this->sales_reps = $sales_reps;
    }

    public function addSalesRep(User $sales_rep) {
        if(!$this->sales_reps->contains($sales_rep))
            $this->sales_reps[] = $sales_rep;
    }

    public function removeSalesRep($salesRep)
    {
        $this->sales_reps->removeElement($salesRep);
    }


    /**
     * @return mixed
     */
    public function getMySalesManager()
    {
        return $this->my_sales_manager;
    }

    /**
     * @param mixed $my_sales_manager
     */
    public function setMySalesManager($my_sales_manager)
    {
        $this->my_sales_manager = $my_sales_manager;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubmittedOrders()
    {
        return $this->submitted_orders;
    }

    /**
     * @param mixed $submitted_orders
     */
    public function setSubmittedOrders($submitted_orders)
    {
        $this->submitted_orders = $submitted_orders;
    }

    /**
     * @return ArrayCollection
     */
    public function getLedgers()
    {
        return $this->ledgers;
    }

    /**
     * @param ArrayCollection $ledgers
     */
    public function setLedgers($ledgers)
    {
        $this->ledgers = $ledgers;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubmittedLedgers()
    {
        return $this->submitted_ledgers;
    }

    /**
     * @param ArrayCollection $submittedLedgers
     */
    public function setSubmittedLedgers($submittedLedgers)
    {
        $this->submitted_ledgers = $submittedLedgers;
    }

    /**
     * @return ArrayCollection
     */
    public function getCreditedLedgers()
    {
        return $this->credited_ledgers;
    }

    /**
     * @param ArrayCollection $creditedLedgers
     */
    public function setCreditedLedgers($creditedLedgers)
    {
        $this->credited_ledgers = $creditedLedgers;
    }

    /**
     * @return ArrayCollection
     */
    public function getRebateSubmissions()
    {
        return $this->rebate_submissions;
    }

    /**
     * @param ArrayCollection $rebate_submissions
     */
    public function setRebateSubmissions($rebate_submissions)
    {
        $this->rebate_submissions = $rebate_submissions;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubmittedRebates()
    {
        return $this->submitted_rebates;
    }

    /**
     * @param ArrayCollection $submitted_rebates
     */
    public function setSubmittedRebates($submitted_rebates)
    {
        $this->submitted_rebates = $submitted_rebates;
    }

    /**
     * @return Channel
     */
    public function getActiveChannel()
    {
        return $this->active_channel;
    }

    /**
     * @param Channel $active_channel
     */
    public function setActiveChannel(Channel $active_channel)
    {
        $this->active_channel = $active_channel;
    }

    /**
     * @return mixed
     */
    public function getDistributorFedexNumber()
    {
        return $this->distributor_fedex_number;
    }

    /**
     * @param mixed $distributor_fedex_number
     */
    public function setDistributorFedexNumber($distributor_fedex_number)
    {
        $this->distributor_fedex_number = $distributor_fedex_number;
    }


    /**
     * Set achRoutingNumber
     *
     * @param integer $achRoutingNumber
     *
     * @return User
     */
    public function setAchRoutingNumber($achRoutingNumber)
    {
        $this->ach_routing_number = $achRoutingNumber;

        return $this;
    }

    /**
     * Get achRoutingNumber
     *
     * @return integer
     */
    public function getAchRoutingNumber()
    {
        return $this->ach_routing_number;
    }

    /**
     * Set achAccountNumber
     *
     * @param string $achAccountNumber
     *
     * @return User
     */
    public function setAchAccountNumber($achAccountNumber)
    {
        $this->ach_account_number = $achAccountNumber;

        return $this;
    }

    /**
     * Get achAccountNumber
     *
     * @return string
     */
    public function getAchAccountNumber()
    {
        return $this->ach_account_number;
    }

    /**
     * Add rebateSubmission
     *
     * @param \InventoryBundle\Entity\RebateSubmission $rebateSubmission
     *
     * @return User
     */
    public function addRebateSubmission(\InventoryBundle\Entity\RebateSubmission $rebateSubmission)
    {
        $this->rebate_submissions[] = $rebateSubmission;

        return $this;
    }

    /**
     * Remove rebateSubmission
     *
     * @param \InventoryBundle\Entity\RebateSubmission $rebateSubmission
     */
    public function removeRebateSubmission(\InventoryBundle\Entity\RebateSubmission $rebateSubmission)
    {
        $this->rebate_submissions->removeElement($rebateSubmission);
    }

    /**
     * Add submittedRebate
     *
     * @param \InventoryBundle\Entity\RebateSubmission $submittedRebate
     *
     * @return User
     */
    public function addSubmittedRebate(\InventoryBundle\Entity\RebateSubmission $submittedRebate)
    {
        $this->submitted_rebates[] = $submittedRebate;

        return $this;
    }

    /**
     * Remove submittedRebate
     *
     * @param \InventoryBundle\Entity\RebateSubmission $submittedRebate
     */
    public function removeSubmittedRebate(\InventoryBundle\Entity\RebateSubmission $submittedRebate)
    {
        $this->submitted_rebates->removeElement($submittedRebate);
    }

    /**
     * Add warrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $warrantyClaim
     *
     * @return User
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
     * Add submittedWarrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $submittedWarrantyClaim
     *
     * @return User
     */
    public function addSubmittedWarrantyClaim(\InventoryBundle\Entity\WarrantyClaim $submittedWarrantyClaim)
    {
        $this->submitted_warranty_claims[] = $submittedWarrantyClaim;

        return $this;
    }

    /**
     * Remove submittedWarrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $submittedWarrantyClaim
     */
    public function removeSubmittedWarrantyClaim(\InventoryBundle\Entity\WarrantyClaim $submittedWarrantyClaim)
    {
        $this->submitted_warranty_claims->removeElement($submittedWarrantyClaim);
    }

    /**
     * Add purchaseOrder
     *
     * @param \WarehouseBundle\Entity\PurchaseOrder $purchaseOrder
     *
     * @return User
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
     * Add order
     *
     * @param \OrderBundle\Entity\Orders $order
     *
     * @return User
     */
    public function addOrder(\OrderBundle\Entity\Orders $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \OrderBundle\Entity\Orders $order
     */
    public function removeOrder(\OrderBundle\Entity\Orders $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Add submittedOrder
     *
     * @param \OrderBundle\Entity\Orders $submittedOrder
     *
     * @return User
     */
    public function addSubmittedOrder(\OrderBundle\Entity\Orders $submittedOrder)
    {
        $this->submitted_orders[] = $submittedOrder;

        return $this;
    }

    /**
     * Remove submittedOrder
     *
     * @param \OrderBundle\Entity\Orders $submittedOrder
     */
    public function removeSubmittedOrder(\OrderBundle\Entity\Orders $submittedOrder)
    {
        $this->submitted_orders->removeElement($submittedOrder);
    }

    /**
     * Add stockTransfer
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransfer
     *
     * @return User
     */
    public function addStockTransfer(\WarehouseBundle\Entity\StockTransfer $stockTransfer)
    {
        $this->stock_transfers[] = $stockTransfer;

        return $this;
    }

    /**
     * Remove stockTransfer
     *
     * @param \WarehouseBundle\Entity\StockTransfer $stockTransfer
     */
    public function removeStockTransfer(\WarehouseBundle\Entity\StockTransfer $stockTransfer)
    {
        $this->stock_transfers->removeElement($stockTransfer);
    }

    /**
     * Add stockAdjustment
     *
     * @param \WarehouseBundle\Entity\StockAdjustment $stockAdjustment
     *
     * @return User
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
     * Add ledger
     *
     * @param \OrderBundle\Entity\Ledger $ledger
     *
     * @return User
     */
    public function addLedger(\OrderBundle\Entity\Ledger $ledger)
    {
        $this->ledgers[] = $ledger;

        return $this;
    }

    /**
     * Remove ledger
     *
     * @param \OrderBundle\Entity\Ledger $ledger
     */
    public function removeLedger(\OrderBundle\Entity\Ledger $ledger)
    {
        $this->ledgers->removeElement($ledger);
    }

    /**
     * Add submittedLedger
     *
     * @param \OrderBundle\Entity\Ledger $submittedLedger
     *
     * @return User
     */
    public function addSubmittedLedger(\OrderBundle\Entity\Ledger $submittedLedger)
    {
        $this->submitted_ledgers[] = $submittedLedger;

        return $this;
    }

    /**
     * Remove submittedLedger
     *
     * @param \OrderBundle\Entity\Ledger $submittedLedger
     */
    public function removeSubmittedLedger(\OrderBundle\Entity\Ledger $submittedLedger)
    {
        $this->submitted_ledgers->removeElement($submittedLedger);
    }

    /**
     * Add creditedLedger
     *
     * @param \OrderBundle\Entity\Ledger $creditedLedger
     *
     * @return User
     */
    public function addCreditedLedger(\OrderBundle\Entity\Ledger $creditedLedger)
    {
        $this->credited_ledgers[] = $creditedLedger;

        return $this;
    }

    /**
     * Remove creditedLedger
     *
     * @param \OrderBundle\Entity\Ledger $creditedLedger
     */
    public function removeCreditedLedger(\OrderBundle\Entity\Ledger $creditedLedger)
    {
        $this->credited_ledgers->removeElement($creditedLedger);
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
     * Set hideRebate
     *
     * @param boolean $hideRebate
     *
     * @return User
     */
    public function setHideRebate($hideRebate)
    {
        $this->hideRebate = $hideRebate;

        return $this;
    }

    /**
     * Get hideRebate
     *
     * @return boolean
     */
    public function getHideRebate()
    {
        return $this->hideRebate;
    }

    /**
     * Add managedWarehouse
     *
     * @param \WarehouseBundle\Entity\Warehouse $managedWarehouse
     *
     * @return User
     */
    public function addManagedWarehouse(\WarehouseBundle\Entity\Warehouse $managedWarehouse)
    {
        $this->managed_warehouses[] = $managedWarehouse;

        return $this;
    }

    /**
     * Remove managedWarehouse
     *
     * @param \WarehouseBundle\Entity\Warehouse $managedWarehouse
     */
    public function removeManagedWarehouse(\WarehouseBundle\Entity\Warehouse $managedWarehouse)
    {
        $this->managed_warehouses->removeElement($managedWarehouse);
    }

    /**
     * Add promoKitOrder
     *
     * @param \InventoryBundle\Entity\PromoKitOrders $promoKitOrder
     *
     * @return User
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
     * Returns either company name or first/last name if company name is empty.
     * @return mixed|string
     */
    public function getDisplayName() {
        if ( empty($this->company_name) ) {
            return $this->getName();
        } else {
            return $this->getCompanyName();
        }
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * Add warehouse
     *
     * @param \WarehouseBundle\Entity\Warehouse $warehouse
     *
     * @return User
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

    /**
     * Add creditRequestFor
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequestFor
     *
     * @return User
     */
    public function addCreditRequestFor(\OrderBundle\Entity\CreditRequest $creditRequestFor)
    {
        $this->creditRequestFor[] = $creditRequestFor;

        return $this;
    }

    /**
     * Remove creditRequestFor
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequestFor
     */
    public function removeCreditRequestFor(\OrderBundle\Entity\CreditRequest $creditRequestFor)
    {
        $this->creditRequestFor->removeElement($creditRequestFor);
    }

    /**
     * Get creditRequestFor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditRequestFor()
    {
        return $this->creditRequestFor;
    }

    /**
     * Add creditRequestBy
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequestBy
     *
     * @return User
     */
    public function addCreditRequestBy(\OrderBundle\Entity\CreditRequest $creditRequestBy)
    {
        $this->creditRequestBy[] = $creditRequestBy;

        return $this;
    }

    /**
     * Remove creditRequestBy
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequestBy
     */
    public function removeCreditRequestBy(\OrderBundle\Entity\CreditRequest $creditRequestBy)
    {
        $this->creditRequestBy->removeElement($creditRequestBy);
    }

    /**
     * Get creditRequestBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditRequestBy()
    {
        return $this->creditRequestBy;
    }
}
