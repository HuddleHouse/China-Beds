<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @var string
     *
     * @ORM\Column(name="front_logo", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_link", type="string", length=255)
     */
    private $fbLink;

    /**
     * @var string
     *
     * @ORM\Column(name="tw_link", type="string", length=255)
     */
    private $twLink;

    /**
     * @var string
     *
     * @ORM\Column(name="insta_link", type="string", length=255)
     */
    private $instaLink;

    /**
     * @var string
     *
     * @ORM\Column(name="front_slider_one", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontSliderOne;

    /**
     * @var string
     *
     * @ORM\Column(name="front_slider_two", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontSliderTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="front_slider_three", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontSliderThree;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_one", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontFooterOne;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_two", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontFooterTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_three", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $frontFooterThree;







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
     * Set frontLogo
     *
     * @param string $frontLogo
     *
     * @return Channel
     */
    public function setFrontLogo($frontLogo)
    {
        $this->frontLogo = $frontLogo;

        return $this;
    }

    /**
     * Get frontLogo
     *
     * @return string
     */
    public function getFrontLogo()
    {
        return $this->frontLogo;
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
     * Set fbLink
     *
     * @param string $fbLink
     *
     * @return Channel
     */
    public function setFbLink($fbLink)
    {
        $this->fbLink = $fbLink;

        return $this;
    }

    /**
     * Get fbLink
     *
     * @return string
     */
    public function getFbLink()
    {
        return $this->fbLink;
    }

    /**
     * Set twLink
     *
     * @param string $twLink
     *
     * @return Channel
     */
    public function setTwLink($twLink)
    {
        $this->twLink = $twLink;

        return $this;
    }

    /**
     * Get twLink
     *
     * @return string
     */
    public function getTwLink()
    {
        return $this->twLink;
    }

    /**
     * Set instaLink
     *
     * @param string $instaLink
     *
     * @return Channel
     */
    public function setInstaLink($instaLink)
    {
        $this->instaLink = $instaLink;

        return $this;
    }

    /**
     * Get instaLink
     *
     * @return string
     */
    public function getInstaLink()
    {
        return $this->instaLink;
    }

    /**
     * Set frontSliderOne
     *
     * @param string $frontSliderOne
     *
     * @return Channel
     */
    public function setFrontSliderOne($frontSliderOne)
    {
        $this->frontSliderOne = $frontSliderOne;

        return $this;
    }

    /**
     * Get frontSliderOne
     *
     * @return string
     */
    public function getFrontSliderOne()
    {
        return $this->frontSliderOne;
    }

    /**
     * Set frontSliderTwo
     *
     * @param string $frontSliderTwo
     *
     * @return Channel
     */
    public function setFrontSliderTwo($frontSliderTwo)
    {
        $this->frontSliderTwo = $frontSliderTwo;

        return $this;
    }

    /**
     * Get frontSliderTwo
     *
     * @return string
     */
    public function getFrontSliderTwo()
    {
        return $this->frontSliderTwo;
    }

    /**
     * Set frontSliderThree
     *
     * @param string $frontSliderThree
     *
     * @return Channel
     */
    public function setFrontSliderThree($frontSliderThree)
    {
        $this->frontSliderThree = $frontSliderThree;

        return $this;
    }

    /**
     * Get frontSliderThree
     *
     * @return string
     */
    public function getFrontSliderThree()
    {
        return $this->frontSliderThree;
    }

    /**
     * Set frontFooterOne
     *
     * @param string $frontFooterOne
     *
     * @return Channel
     */
    public function setFrontFooterOne($frontFooterOne)
    {
        $this->frontFooterOne = $frontFooterOne;

        return $this;
    }

    /**
     * Get frontFooterOne
     *
     * @return string
     */
    public function getFrontFooterOne()
    {
        return $this->frontFooterOne;
    }

    /**
     * Set frontFooterTwo
     *
     * @param string $frontFooterTwo
     *
     * @return Channel
     */
    public function setFrontFooterTwo($frontFooterTwo)
    {
        $this->frontFooterTwo = $frontFooterTwo;

        return $this;
    }

    /**
     * Get frontFooterTwo
     *
     * @return string
     */
    public function getFrontFooterTwo()
    {
        return $this->frontFooterTwo;
    }

    /**
     * Set frontFooterThree
     *
     * @param string $frontFooterThree
     *
     * @return Channel
     */
    public function setFrontFooterThree($frontFooterThree)
    {
        $this->frontFooterThree = $frontFooterThree;

        return $this;
    }

    /**
     * Get frontFooterThree
     *
     * @return string
     */
    public function getFrontFooterThree()
    {
        return $this->frontFooterThree;
    }
}
