<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity
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
    protected $is_current = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_online_intentions = true;

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
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\RebateSubmission", mappedBy="user")
     */
    private $rebate_submissions;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="user")
     */
    private $warranty_claims;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\PurchaseOrder", mappedBy="user")
     */
    private $purchase_orders;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\StockTransfer", mappedBy="user")
     */
    private $stock_transfers;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\StockAdjustment", mappedBy="user")
     */
    private $stock_adjustments;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="users_1")
     * @ORM\JoinColumn(name="warehouse_1", referencedColumnName="id")
     */
    private $warehouse_1;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="users_2")
     * @ORM\JoinColumn(name="warehouse_2", referencedColumnName="id")
     */
    private $warehouse_2;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Warehouse", inversedBy="users_3")
     * @ORM\JoinColumn(name="warehouse_3", referencedColumnName="id")
     */
    private $warehouse_3;

    public function __construct()
    {
        parent::__construct();
        
        $this->warranty_claims = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_channels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rebate_submissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->price_groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->purchase_orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stock_adjustments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getRouteNames() {
        foreach($this->groups as $role) {
            foreach($role->getPermissions() as $permission) {
                $data[$permission->getRouteName()] = $permission->getRouteName();
            }
        }
        return $data;
    }

    public function getUserChannelsArray() {
        foreach($this->user_channels as $channel)
            $data[$channel->getId()] = array(
                'id' => $channel->getId(),
                'name' => $channel->getName(),
                'url' => $channel->getUrl()
            );
        return $data;
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
    public function getIsCurrent()
    {
        return $this->is_current;
    }

    /**
     * @param mixed $is_current
     */
    public function setIsCurrent($is_current)
    {
        $this->is_current = $is_current;
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
     * @param \AppBundle\Entity\User $user
     */
    public function removePriceGroup(\AppBundle\Entity\PriceGroup $priceGroup)
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
     * @param \AppBundle\Entity\User $user
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
    public function getRebateSubmissions()
    {
        return $this->rebate_submissions;
    }

    /**
     * @param mixed $rebate_submissions
     */
    public function setRebateSubmissions($rebate_submissions)
    {
        $this->rebate_submissions = $rebate_submissions;
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


}