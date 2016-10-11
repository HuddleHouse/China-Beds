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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

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
}

