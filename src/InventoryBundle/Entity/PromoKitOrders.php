<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PromoKitOrders
 *
 * @ORM\Table(name="promo_kit_orders")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PromoKitOrdersRepository")
 */
class PromoKitOrders
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
     * @ORM\Column(name="retailer_store_name", type="string", length=255)
     */
    private $retailerStoreName;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_contact", type="string", length=255)
     */
    private $shipContact;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_address", type="string", length=255)
     */
    private $shipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_address2", type="string", length=255, nullable=true)
     */
    private $shipAddress2;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_city", type="string", length=255)
     */
    private $shipCity;

    /**
     * @var int
     *
     * @ORM\Column(name="ship_zip", type="integer", nullable=true)
     */
    private $shipZip;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_phone", type="string", length=255, nullable=true)
     */
    private $shipPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\PromoKit", mappedBy="promoKitOrder")
     */
    private $promoKitItems;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="promo_kit_orders")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersProductVariant", mappedBy="promo_kit_order")
     */
    private $productVariants;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersPopItem", mappedBy="promo_kit_order")
     */
    private $popItems;

    /**
     * PromoKit constructor.
     */
    public function __construct()
    {
        $this->promoKitItems = new ArrayCollection();
        $this->productVariants = new ArrayCollection();
        $this->popItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getRetailerStoreName()
    {
        return $this->retailerStoreName;
    }

    /**
     * @param string $retailerStoreName
     */
    public function setRetailerStoreName($retailerStoreName)
    {
        $this->retailerStoreName = $retailerStoreName;
    }

    /**
     * @return string
     */
    public function getShipContact()
    {
        return $this->shipContact;
    }

    /**
     * @param string $shipContact
     */
    public function setShipContact($shipContact)
    {
        $this->shipContact = $shipContact;
    }

    /**
     * @return string
     */
    public function getShipAddress()
    {
        return $this->shipAddress;
    }

    /**
     * @param string $shipAddress
     */
    public function setShipAddress($shipAddress)
    {
        $this->shipAddress = $shipAddress;
    }

    /**
     * @return string
     */
    public function getShipAddress2()
    {
        return $this->shipAddress2;
    }

    /**
     * @param string $shipAddress2
     */
    public function setShipAddress2($shipAddress2)
    {
        $this->shipAddress2 = $shipAddress2;
    }

    /**
     * @return string
     */
    public function getShipCity()
    {
        return $this->shipCity;
    }

    /**
     * @param string $shipCity
     */
    public function setShipCity($shipCity)
    {
        $this->shipCity = $shipCity;
    }

    /**
     * @return int
     */
    public function getShipZip()
    {
        return $this->shipZip;
    }

    /**
     * @param int $shipZip
     */
    public function setShipZip($shipZip)
    {
        $this->shipZip = $shipZip;
    }

    /**
     * @return string
     */
    public function getShipPhone()
    {
        return $this->shipPhone;
    }

    /**
     * @param string $shipPhone
     */
    public function setShipPhone($shipPhone)
    {
        $this->shipPhone = $shipPhone;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPromoKitItems()
    {
        return $this->promoKitItems;
    }

    /**
     * @param mixed $promoKitItems
     */
    public function setPromoKitItems($promoKitItems)
    {
        $this->promoKitItems = $promoKitItems;
    }

    /**
     * @return mixed
     */
    public function getSubmittedByUser()
    {
        return $this->submittedByUser;
    }

    /**
     * @param mixed $submittedByUser
     */
    public function setSubmittedByUser($submittedByUser)
    {
        $this->submittedByUser = $submittedByUser;
    }

    /**
     * @return mixed
     */
    public function getProductVariants()
    {
        return $this->productVariants;
    }

    /**
     * @param mixed $productVariants
     */
    public function setProductVariants($productVariants)
    {
        $this->productVariants = $productVariants;
    }

    /**
     * @return mixed
     */
    public function getPopItems()
    {
        return $this->popItems;
    }

    /**
     * @param mixed $popItems
     */
    public function setPopItems($popItems)
    {
        $this->popItems = $popItems;
    }
}
