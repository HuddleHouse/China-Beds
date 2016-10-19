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
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
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
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontSliderOne;

    /**
     * @var string
     *
     * @ORM\Column(name="front_slider_two", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontSliderTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="front_slider_three", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontSliderThree;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_one", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontFooterOne;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_two", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontFooterTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_three", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $frontFooterThree;

    /**
     * @var string
     *
     * @ORM\Column(name="front_footer_text", type="string")
     */
    private $frontFooterText;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_warranty_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqWarrantyPic;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_unpacking_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqUnpackingPic;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_support_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqSupportPic;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_maintenance_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqMaintenancePic;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_contact_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqContactPic;

    /**
     * @var string
     *
     * @ORM\Column(name="faq_tc_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $faqTCPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_memoryfoam_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFmemoryFoamPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_side_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFSidePic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_renewresource_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFRenewResourcewPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_socs_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFsocsPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_pbo_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFpboPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_bcharcoal_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFBCharcoalPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_bfibers_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFBFibersPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_silk_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFSilkPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_aloevera_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFAloeVeraPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_certified_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFCertifiedPic;

    /**
     * @var string
     *
     * @ORM\Column(name="pf_texstand_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $pFTexStandPic;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_header_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $retailHeaderPic;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_first_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $retailFirstPic;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_second_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $retailSecondPic;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_third_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $retailThirdPic;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_fourth_pic", type="string")
     *
     * @Assert\NotBlank(message="Please, upload logo as either a jpg or png file.")
     * @Assert\File(mimeTypes={ "image/png", "image/jpg" })
     */
    private $retailFourthPic;







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

    /**
     * Set frontFooterText
     *
     * @param string $frontFooterText
     *
     * @return Channel
     */
    public function setFrontFooterText($frontFooterText)
    {
        $this->frontFooterText = $frontFooterText;

        return $this;
    }

    /**
     * Get frontFooterText
     *
     * @return string
     */
    public function getFrontFooterText()
    {
        return $this->frontFooterText;
    }

    /**
     * Set faqWarrantyPic
     *
     * @param string $faqWarrantyPic
     *
     * @return Channel
     */
    public function setFaqWarrantyPic($faqWarrantyPic)
    {
        $this->faqWarrantyPic = $faqWarrantyPic;

        return $this;
    }

    /**
     * Get faqWarrantyPic
     *
     * @return string
     */
    public function getFaqWarrantyPic()
    {
        return $this->faqWarrantyPic;
    }

    /**
     * Set faqUnpackingPic
     *
     * @param string $faqUnpackingPic
     *
     * @return Channel
     */
    public function setFaqUnpackingPic($faqUnpackingPic)
    {
        $this->faqUnpackingPic = $faqUnpackingPic;

        return $this;
    }

    /**
     * Get faqUnpackingPic
     *
     * @return string
     */
    public function getFaqUnpackingPic()
    {
        return $this->faqUnpackingPic;
    }

    /**
     * Set faqSupportPic
     *
     * @param string $faqSupportPic
     *
     * @return Channel
     */
    public function setFaqSupportPic($faqSupportPic)
    {
        $this->faqSupportPic = $faqSupportPic;

        return $this;
    }

    /**
     * Get faqSupportPic
     *
     * @return string
     */
    public function getFaqSupportPic()
    {
        return $this->faqSupportPic;
    }

    /**
     * Set faqMaintenancePic
     *
     * @param string $faqMaintenancePic
     *
     * @return Channel
     */
    public function setFaqMaintenancePic($faqMaintenancePic)
    {
        $this->faqMaintenancePic = $faqMaintenancePic;

        return $this;
    }

    /**
     * Get faqMaintenancePic
     *
     * @return string
     */
    public function getFaqMaintenancePic()
    {
        return $this->faqMaintenancePic;
    }

    /**
     * Set faqContactPic
     *
     * @param string $faqContactPic
     *
     * @return Channel
     */
    public function setFaqContactPic($faqContactPic)
    {
        $this->faqContactPic = $faqContactPic;

        return $this;
    }

    /**
     * Get faqContactPic
     *
     * @return string
     */
    public function getFaqContactPic()
    {
        return $this->faqContactPic;
    }

    /**
     * Set faqTCPic
     *
     * @param string $faqTCPic
     *
     * @return Channel
     */
    public function setFaqTCPic($faqTCPic)
    {
        $this->faqTCPic = $faqTCPic;

        return $this;
    }

    /**
     * Get faqTCPic
     *
     * @return string
     */
    public function getFaqTCPic()
    {
        return $this->faqTCPic;
    }

    /**
     * Set pFmemoryFoamPic
     *
     * @param string $pFmemoryFoamPic
     *
     * @return Channel
     */
    public function setPFmemoryFoamPic($pFmemoryFoamPic)
    {
        $this->pFmemoryFoamPic = $pFmemoryFoamPic;

        return $this;
    }

    /**
     * Get pFmemoryFoamPic
     *
     * @return string
     */
    public function getPFmemoryFoamPic()
    {
        return $this->pFmemoryFoamPic;
    }

    /**
     * Set pFRenewResourcewPic
     *
     * @param string $pFRenewResourcewPic
     *
     * @return Channel
     */
    public function setPFRenewResourcewPic($pFRenewResourcewPic)
    {
        $this->pFRenewResourcewPic = $pFRenewResourcewPic;

        return $this;
    }

    /**
     * Get pFRenewResourcewPic
     *
     * @return string
     */
    public function getPFRenewResourcewPic()
    {
        return $this->pFRenewResourcewPic;
    }

    /**
     * Set pFsocsPic
     *
     * @param string $pFsocsPic
     *
     * @return Channel
     */
    public function setPFsocsPic($pFsocsPic)
    {
        $this->pFsocsPic = $pFsocsPic;

        return $this;
    }

    /**
     * Get pFsocsPic
     *
     * @return string
     */
    public function getPFsocsPic()
    {
        return $this->pFsocsPic;
    }

    /**
     * Set pFpboPic
     *
     * @param string $pFpboPic
     *
     * @return Channel
     */
    public function setPFpboPic($pFpboPic)
    {
        $this->pFpboPic = $pFpboPic;

        return $this;
    }

    /**
     * Get pFpboPic
     *
     * @return string
     */
    public function getPFpboPic()
    {
        return $this->pFpboPic;
    }

    /**
     * Set pFBCharcoalPic
     *
     * @param string $pFBCharcoalPic
     *
     * @return Channel
     */
    public function setPFBCharcoalPic($pFBCharcoalPic)
    {
        $this->pFBCharcoalPic = $pFBCharcoalPic;

        return $this;
    }

    /**
     * Get pFBCharcoalPic
     *
     * @return string
     */
    public function getPFBCharcoalPic()
    {
        return $this->pFBCharcoalPic;
    }

    /**
     * Set pFBFibersPic
     *
     * @param string $pFBFibersPic
     *
     * @return Channel
     */
    public function setPFBFibersPic($pFBFibersPic)
    {
        $this->pFBFibersPic = $pFBFibersPic;

        return $this;
    }

    /**
     * Get pFBFibersPic
     *
     * @return string
     */
    public function getPFBFibersPic()
    {
        return $this->pFBFibersPic;
    }

    /**
     * Set pFSilkPic
     *
     * @param string $pFSilkPic
     *
     * @return Channel
     */
    public function setPFSilkPic($pFSilkPic)
    {
        $this->pFSilkPic = $pFSilkPic;

        return $this;
    }

    /**
     * Get pFSilkPic
     *
     * @return string
     */
    public function getPFSilkPic()
    {
        return $this->pFSilkPic;
    }

    /**
     * Set pFAloeVeraPic
     *
     * @param string $pFAloeVeraPic
     *
     * @return Channel
     */
    public function setPFAloeVeraPic($pFAloeVeraPic)
    {
        $this->pFAloeVeraPic = $pFAloeVeraPic;

        return $this;
    }

    /**
     * Get pFAloeVeraPic
     *
     * @return string
     */
    public function getPFAloeVeraPic()
    {
        return $this->pFAloeVeraPic;
    }

    /**
     * Set pFCertifiedPic
     *
     * @param string $pFCertifiedPic
     *
     * @return Channel
     */
    public function setPFCertifiedPic($pFCertifiedPic)
    {
        $this->pFCertifiedPic = $pFCertifiedPic;

        return $this;
    }

    /**
     * Get pFCertifiedPic
     *
     * @return string
     */
    public function getPFCertifiedPic()
    {
        return $this->pFCertifiedPic;
    }

    /**
     * Set pFTexStandPic
     *
     * @param string $pFTexStandPic
     *
     * @return Channel
     */
    public function setPFTexStandPic($pFTexStandPic)
    {
        $this->pFTexStandPic = $pFTexStandPic;

        return $this;
    }

    /**
     * Get pFTexStandPic
     *
     * @return string
     */
    public function getPFTexStandPic()
    {
        return $this->pFTexStandPic;
    }

    /**
     * Set pFSidePic
     *
     * @param string $pFSidePic
     *
     * @return Channel
     */
    public function setPFSidePic($pFSidePic)
    {
        $this->pFSidePic = $pFSidePic;

        return $this;
    }

    /**
     * Get pFSidePic
     *
     * @return string
     */
    public function getPFSidePic()
    {
        return $this->pFSidePic;
    }

    /**
     * Set retailHeaderPic
     *
     * @param string $retailHeaderPic
     *
     * @return Channel
     */
    public function setRetailHeaderPic($retailHeaderPic)
    {
        $this->retailHeaderPic = $retailHeaderPic;

        return $this;
    }

    /**
     * Get retailHeaderPic
     *
     * @return string
     */
    public function getRetailHeaderPic()
    {
        return $this->retailHeaderPic;
    }

    /**
     * Set retailFirstPic
     *
     * @param string $retailFirstPic
     *
     * @return Channel
     */
    public function setRetailFirstPic($retailFirstPic)
    {
        $this->retailFirstPic = $retailFirstPic;

        return $this;
    }

    /**
     * Get retailFirstPic
     *
     * @return string
     */
    public function getRetailFirstPic()
    {
        return $this->retailFirstPic;
    }

    /**
     * Set retailSecondPic
     *
     * @param string $retailSecondPic
     *
     * @return Channel
     */
    public function setRetailSecondPic($retailSecondPic)
    {
        $this->retailSecondPic = $retailSecondPic;

        return $this;
    }

    /**
     * Get retailSecondPic
     *
     * @return string
     */
    public function getRetailSecondPic()
    {
        return $this->retailSecondPic;
    }

    /**
     * Set retailThirdPic
     *
     * @param string $retailThirdPic
     *
     * @return Channel
     */
    public function setRetailThirdPic($retailThirdPic)
    {
        $this->retailThirdPic = $retailThirdPic;

        return $this;
    }

    /**
     * Get retailThirdPic
     *
     * @return string
     */
    public function getRetailThirdPic()
    {
        return $this->retailThirdPic;
    }

    /**
     * Set retailFourthPic
     *
     * @param string $retailFourthPic
     *
     * @return Channel
     */
    public function setRetailFourthPic($retailFourthPic)
    {
        $this->retailFourthPic = $retailFourthPic;

        return $this;
    }

    /**
     * Get retailFourthPic
     *
     * @return string
     */
    public function getRetailFourthPic()
    {
        return $this->retailFourthPic;
    }
}
