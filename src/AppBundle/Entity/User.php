<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    protected $company_name;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     *
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\RoleGroup")
     * @ORM\JoinTable(name="user_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your code is invalid.", groups={"Registration"})
     */
    protected $invitation;
    

    public function __construct()
    {
        parent::__construct();
        // your own logic
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

}