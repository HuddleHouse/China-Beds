<?php
namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FrontWarrantyClaim
 *
 * @ORM\Table(name="front_warranty_claim")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\FrontWarrantyClaimRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FrontWarrantyClaim
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_made_aware", type="datetime", nullable=true)
     */
    private $dateMadeAware;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_claim", type="datetime")
     */
    private $dateOfClaim;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone", type="integer", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_date", type="string", length=255, nullable=true)
     */
    private $purchaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="retailer_name", type="string", length=255, nullable=true)
     */
    private $retailerName;

    /**
     * @var int
     *
     * @ORM\Column(name="mattress_model", type="string", length=255, nullable=true)
     */
    private $mattressModel;

    /**
     * @var string
     *
     * @ORM\Column(name="mattress_size", type="string", length=255, nullable=true)
     */
    private $mattressSize;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchase_price", type="integer", length=255, nullable=true)
     */
    private $purchasePrice;

    /**
     * @var bool
     *
     * @ORM\Column(name="contacted_retailer", type="boolean", nullable=true)
     */
    private $contactedRetailer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipping_damage", type="boolean", nullable=true)
     */
    private $shippingDamage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="within_48", type="boolean", nullable=true)
     */
    private $within48;

    /**
     * @var boolean
     *
     * @ORM\Column(name="length_different", type="boolean", nullable=true)
     */
    private $lengthDifferent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="body_impression", type="boolean", nullable=true)
     */
    private $bodyImpression;

    /**
     * @var boolean
     *
     * @ORM\Column(name="feeling_issue", type="boolean", nullable=true)
     */
    private $feelingIssue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pillow_issue", type="boolean", nullable=true)
     */
    private $pillowIssue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="diff_issue", type="boolean", nullable=true)
     */
    private $diffIssue;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255, nullable=true)
     */
    private $channel;




    public function __construct()
    {
        $this->setDateOfClaim(new \DateTime());
        $this->ledgers = new ArrayCollection();
    }

    /**
     * Set dateMadeAware
     *
     * @param \DateTime $dateMadeAware
     *
     * @return WarrantyClaim
     */
    public function setDateMadeAware($dateMadeAware)
    {
        $this->dateMadeAware = $dateMadeAware;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get dateMadeAware
     *
     * @return \DateTime
     */
    public function getDateMadeAware()
    {
        return $this->dateMadeAware;
    }

    /**
     * Set dateOfClaim
     *
     * @param \DateTime $dateOfClaim
     *
     * @return FrontWarrantyClaim
     */
    public function setDateOfClaim($dateOfClaim)
    {
        $this->dateOfClaim = $dateOfClaim;

        return $this;
    }

    /**
     * Get dateOfClaim
     *
     * @return \DateTime
     */
    public function getDateOfClaim()
    {
        return $this->dateOfClaim;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FrontWarrantyClaim
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
     * Set email
     *
     * @param string $email
     *
     * @return FrontWarrantyClaim
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
     * Set address
     *
     * @param string $address
     *
     * @return FrontWarrantyClaim
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return FrontWarrantyClaim
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
     * Set state
     *
     * @param string $state
     *
     * @return FrontWarrantyClaim
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param integer $zip
     *
     * @return FrontWarrantyClaim
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return integer
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return FrontWarrantyClaim
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set purchaseDate
     *
     * @param string $purchaseDate
     *
     * @return FrontWarrantyClaim
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return string
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set retailerName
     *
     * @param string $retailerName
     *
     * @return FrontWarrantyClaim
     */
    public function setRetailerName($retailerName)
    {
        $this->retailerName = $retailerName;

        return $this;
    }

    /**
     * Get retailerName
     *
     * @return string
     */
    public function getRetailerName()
    {
        return $this->retailerName;
    }

    /**
     * Set mattressModel
     *
     * @param string $mattressModel
     *
     * @return FrontWarrantyClaim
     */
    public function setMattressModel($mattressModel)
    {
        $this->mattressModel = $mattressModel;

        return $this;
    }

    /**
     * Get mattressModel
     *
     * @return string
     */
    public function getMattressModel()
    {
        return $this->mattressModel;
    }

    /**
     * Set mattressSize
     *
     * @param string $mattressSize
     *
     * @return FrontWarrantyClaim
     */
    public function setMattressSize($mattressSize)
    {
        $this->mattressSize = $mattressSize;

        return $this;
    }

    /**
     * Get mattressSize
     *
     * @return string
     */
    public function getMattressSize()
    {
        return $this->mattressSize;
    }

    /**
     * Set purchasePrice
     *
     * @param integer $purchasePrice
     *
     * @return FrontWarrantyClaim
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return integer
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set contactedRetailer
     *
     * @param boolean $contactedRetailer
     *
     * @return FrontWarrantyClaim
     */
    public function setContactedRetailer($contactedRetailer)
    {
        $this->contactedRetailer = $contactedRetailer;

        return $this;
    }

    /**
     * Get contactedRetailer
     *
     * @return boolean
     */
    public function getContactedRetailer()
    {
        return $this->contactedRetailer;
    }

    /**
     * Set shippingDamage
     *
     * @param boolean $shippingDamage
     *
     * @return FrontWarrantyClaim
     */
    public function setShippingDamage($shippingDamage)
    {
        $this->shippingDamage = $shippingDamage;

        return $this;
    }

    /**
     * Get shippingDamage
     *
     * @return boolean
     */
    public function getShippingDamage()
    {
        return $this->shippingDamage;
    }

    /**
     * Set within48
     *
     * @param boolean $within48
     *
     * @return FrontWarrantyClaim
     */
    public function setWithin48($within48)
    {
        $this->within48 = $within48;

        return $this;
    }

    /**
     * Get within48
     *
     * @return boolean
     */
    public function getWithin48()
    {
        return $this->within48;
    }

    /**
     * Set lengthDifferent
     *
     * @param boolean $lengthDifferent
     *
     * @return FrontWarrantyClaim
     */
    public function setLengthDifferent($lengthDifferent)
    {
        $this->lengthDifferent = $lengthDifferent;

        return $this;
    }

    /**
     * Get lengthDifferent
     *
     * @return boolean
     */
    public function getLengthDifferent()
    {
        return $this->lengthDifferent;
    }

    /**
     * Set bodyImpression
     *
     * @param boolean $bodyImpression
     *
     * @return FrontWarrantyClaim
     */
    public function setBodyImpression($bodyImpression)
    {
        $this->bodyImpression = $bodyImpression;

        return $this;
    }

    /**
     * Get bodyImpression
     *
     * @return boolean
     */
    public function getBodyImpression()
    {
        return $this->bodyImpression;
    }

    /**
     * Set feelingIssue
     *
     * @param boolean $feelingIssue
     *
     * @return FrontWarrantyClaim
     */
    public function setFeelingIssue($feelingIssue)
    {
        $this->feelingIssue = $feelingIssue;

        return $this;
    }

    /**
     * Get feelingIssue
     *
     * @return boolean
     */
    public function getFeelingIssue()
    {
        return $this->feelingIssue;
    }

    /**
     * Set pillowIssue
     *
     * @param boolean $pillowIssue
     *
     * @return FrontWarrantyClaim
     */
    public function setPillowIssue($pillowIssue)
    {
        $this->pillowIssue = $pillowIssue;

        return $this;
    }

    /**
     * Get pillowIssue
     *
     * @return boolean
     */
    public function getPillowIssue()
    {
        return $this->pillowIssue;
    }

    /**
     * Set diffIssue
     *
     * @param boolean $diffIssue
     *
     * @return FrontWarrantyClaim
     */
    public function setDiffIssue($diffIssue)
    {
        $this->diffIssue = $diffIssue;

        return $this;
    }

    /**
     * Get diffIssue
     *
     * @return boolean
     */
    public function getDiffIssue()
    {
        return $this->diffIssue;
    }

    /**
     * Set channel
     *
     * @param string $channel
     *
     * @return FrontWarrantyClaim
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
