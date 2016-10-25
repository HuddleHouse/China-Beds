<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Channel
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ChannelRepository")
 */
class Channel
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
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $company_name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;


    /**
     * @var int
     *
     * @ORM\Column(name="ach_routing_number", type="string", length=9, nullable=true)
     */
    private $ach_routing_number;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_account_number", type="string", length=17, nullable=true)
     */
    private $ach_account_number;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_company_id", type="string", length=17, nullable=true)
     */
    private $ach_company_id;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_originating_dfi", type="string", length=17, nullable=true)
     */
    private $ach_originating_dfi;

    /**
     * @var int
     *
     * @ORM\Column(name="ach_company_name", type="string", length=25, nullable=true)
     */
    private $ach_company_name;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductChannel", mappedBy="channel")
     */
    private $product_channels;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Orders", mappedBy="channel")
     */
    private $orders;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="user_channels")
     *
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="channel")
     *
     */
    protected $ledgers;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="channel")
     *
     */
    protected $warranty_claims;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\Rebate", mappedBy="channel")
     *
     */
    protected $rebates;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\RebateSubmission", mappedBy="channel")
     *
     */
    protected $rebate_submissions;

    /**
     * Channel constructor.
     */
    public function __construct() {
        $this->product_channels = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->ledgers = new ArrayCollection();
        $this->warranty_claims = new ArrayCollection();
        $this->rebates = new ArrayCollection();
        $this->rebate_submissions = new ArrayCollection();
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
     * @return Channel
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
     * @return string
     */
    public function getCompanyName()
    {
        if($this->company_name != null)
            return $this->company_name;
        return $this->name;
    }

    /**
     * @param string $company_name
     */
    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return ArrayCollection
     */
    public function getProductChannels()
    {
        return $this->product_channels;
    }

    /**
     * @param ArrayCollection $product_attributes
     */
    public function setProductChannels($product_attributes)
    {
        $this->product_channels = $product_attributes;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
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
    public function getWarrantyClaims()
    {
        return $this->warranty_claims;
    }

    /**
     * @param ArrayCollection $warranty_claims
     */
    public function setWarrantyClaims($warranty_claims)
    {
        $this->warranty_claims = $warranty_claims;
    }

    /**
     * @return ArrayCollection
     */
    public function getRebates()
    {
        return $this->rebates;
    }

    /**
     * @return ArrayCollection
     */
    public function getActiveRebates()
    {
        $rtn = new ArrayCollection();
        foreach($this->rebates as $rebate)
            if($rebate->getActive())
                $rtn->add($rebate);

        return $rtn;
    }

    /**
     * @param ArrayCollection $rebates
     */
    public function setRebates($rebates)
    {
        $this->rebates = $rebates;
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
     * Set achRoutingNumber
     *
     * @param integer $achRoutingNumber
     *
     * @return Channel
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
     * @return Channel
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
     * Add productChannel
     *
     * @param \InventoryBundle\Entity\ProductChannel $productChannel
     *
     * @return Channel
     */
    public function addProductChannel(\InventoryBundle\Entity\ProductChannel $productChannel)
    {
        $this->product_channels[] = $productChannel;

        return $this;
    }

    /**
     * Remove productChannel
     *
     * @param \InventoryBundle\Entity\ProductChannel $productChannel
     */
    public function removeProductChannel(\InventoryBundle\Entity\ProductChannel $productChannel)
    {
        $this->product_channels->removeElement($productChannel);
    }

    /**
     * Add order
     *
     * @param \OrderBundle\Entity\Orders $order
     *
     * @return Channel
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
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Channel
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Add ledger
     *
     * @param \OrderBundle\Entity\Ledger $ledger
     *
     * @return Channel
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
     * Add warrantyClaim
     *
     * @param \InventoryBundle\Entity\WarrantyClaim $warrantyClaim
     *
     * @return Channel
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
     * Add rebate
     *
     * @param \InventoryBundle\Entity\Rebate $rebate
     *
     * @return Channel
     */
    public function addRebate(\InventoryBundle\Entity\Rebate $rebate)
    {
        $this->rebates[] = $rebate;

        return $this;
    }

    /**
     * Remove rebate
     *
     * @param \InventoryBundle\Entity\Rebate $rebate
     */
    public function removeRebate(\InventoryBundle\Entity\Rebate $rebate)
    {
        $this->rebates->removeElement($rebate);
    }

    /**
     * Add rebateSubmission
     *
     * @param \InventoryBundle\Entity\RebateSubmission $rebateSubmission
     *
     * @return Channel
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
     * Set achCompanyId
     *
     * @param integer $achCompanyId
     *
     * @return Channel
     */
    public function setAchCompanyId($achCompanyId)
    {
        $this->ach_company_id = $achCompanyId;

        return $this;
    }

    /**
     * Get achCompanyId
     *
     * @return integer
     */
    public function getAchCompanyId()
    {
        return $this->ach_company_id;
    }

    /**
     * Set achOriginatingDfi
     *
     * @param integer $achOriginatingDfi
     *
     * @return Channel
     */
    public function setAchOriginatingDfi($achOriginatingDfi)
    {
        $this->ach_originating_dfi = $achOriginatingDfi;

        return $this;
    }

    /**
     * Get achOriginatingDfi
     *
     * @return integer
     */
    public function getAchOriginatingDfi()
    {
        return $this->ach_originating_dfi;
    }

    /**
     * Set achCompanyName
     *
     * @param string $achCompanyName
     *
     * @return Channel
     */
    public function setAchCompanyName($achCompanyName)
    {
        $this->ach_company_name = $achCompanyName;

        return $this;
    }

    /**
     * Get achCompanyName
     *
     * @return string
     */
    public function getAchCompanyName()
    {
        return $this->ach_company_name;
    }
}
