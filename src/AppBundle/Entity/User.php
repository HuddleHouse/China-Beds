<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\Channel;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

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
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your code is invalid.", groups={"Registration"})
     */
    protected $invitation;

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
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->hasRole('ROLE_RETAILER')) {
            $count = 0;
            foreach($this->getUserChannels() as $channel)
                $count++;
            if ($count > 1) {
                $context->buildViolation('A retailer can only be on one Channel.')
                    ->atPath('user_channels')
                    ->addViolation();
            }
        }
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
            if($order->getStatus()->getName() == 'Pending') {
                $count++;
                break;
            }
        }
        if($count == 0)
            return false;
        else
            return true;
    }

    public function getPendingOrderTotal($channel_id = null) {
        $total = 0;
        foreach($this->orders as $order) {
            if($order->getStatus()->getName() == 'Pending') {
                if($channel_id == null)
                    $total += $order->getTotal();
                else if($order->getChannel()->getId() == $channel_id)
                    $total += $order->getTotal();
            }
        }
        return $total;
    }

    public function getLedgerTotal($channel_id = null) {
        $total = 0;
        foreach($this->ledgers as $ledger) {
            if($channel_id == null)
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

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
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
     * @return mixed
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
        $this->phone = $phone;
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
                if ( $retailer->belongsToChannel($this->getActiveChannel()) )
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
                if ( $distributor->belongsToChannel($this->getActiveChannel()) )
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
                if ( $rep->belongsToChannel($this->getActiveChannel()) )
                    $data[] = $rep;
        return $data;
    }

    public function belongsToChannel(Channel $channel) {
        foreach($this->getUserChannels() as $user_channel) {
            if ( $user_channel->getId() == $channel->getId() ) {
                return true;
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
     * @return mixed
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
     * @return null
     */
    public function getActiveChannel()
    {
        return $this->active_channel;
    }

    /**
     * @param null $active_channel
     */
    public function setActiveChannel(Channel $active_channel)
    {
        $this->active_channel = $active_channel;
    }



}