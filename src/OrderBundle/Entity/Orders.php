<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrdersRepository")
 */
class Orders
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
     * @ORM\Column(name="order_number", type="string", length=255, nullable=true)
     */
    private $orderNumber;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Status", inversedBy="stock_adjustments")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submit_date", type="datetime", nullable=true)
     */
    private $submitDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pick_up_date", type="datetime", nullable=true)
     */
    private $pickUpDate;

    /**
     * @var string
     *
     * @ORM\Column(name="pick_up_agent", type="string", length=255, nullable=true)
     */
    private $pickUpAgent;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_pick_up", type="boolean", nullable=true)
     */
    private $isPickUp;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_name", type="string", length=255, nullable=true)
     */
    private $shipName;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_address", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="ship_city", type="string", length=255, nullable=true)
     */
    private $ship_city;

    /**
     * @var int
     *
     * @ORM\Column(name="ship_zip", type="integer", nullable=true)
     */
    private $ship_zip;

    /**
     * @var int
     *
     * @ORM\Column(name="discount", type="integer", nullable=true)
     */
    private $discount;

    /**
     * @var int
     *
     * @ORM\Column(name="shipping", type="integer", nullable=true)
     */
    private $shipping;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_phone", type="string", length=255, nullable=true)
     */
    private $shipPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_email", type="string", length=255, nullable=true)
     */
    private $shipEmail;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersProductVariant", mappedBy="order")
     */
    private $product_variants;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersPopItem", mappedBy="order")
     */
    private $pop_items;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="submitted_orders")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submitted_by_user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submitted_for_user;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="orders")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=255, nullable=true)
     */
    private $payment_type;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_paid", type="integer", nullable=true)
     */
    private $amount_paid;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_description", type="string", length=255, nullable=true)
     */
    private $ship_description;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_code", type="string", length=255, nullable=true)
     */
    private $ship_code;

    public function __construct($info = null)
    {
        $this->product_variants = new ArrayCollection();
        $this->pop_items = new ArrayCollection();
        $this->submitDate = new \DateTime();
        if($info != null) {
            if(isset($info['po']))
                $this->orderNumber = $info['po'];
            if(isset($info['pick_up_date']))
                $this->pickUpDate = new \DateTime($info['pick_up_date']);
            if(isset($info['agent_name']))
                $this->pickUpAgent = $info['agent_name'];

            if(isset($info['pick_up']) && $info['pick_up'] == 'true')
                $this->isPickUp = 1;
            else if(isset($info['ship']) && $info['ship'] == 'true')
                $this->isPickUp = 0;

            if(isset($info['comments']))
                $this->comments = $info['comments'];
            if(isset($info['ship_name']))
                $this->shipName = $info['ship_name'];
            if(isset($info['address']))
                $this->shipAddress = $info['address'];
            if(isset($info['address2']))
                $this->shipAddress2 = $info['address2'];
            if(isset($info['city']))
                $this->ship_city = $info['city'];
            if(isset($info['zip']))
                $this->ship_zip = $info['zip'];
            if(isset($info['phone']))
                $this->shipPhone = $info['phone'];
            if(isset($info['email']))
                $this->shipEmail = $info['email'];

        }
    }

    public function setData($info = null)
    {
        $this->product_variants = new ArrayCollection();
        $this->pop_items = new ArrayCollection();
        $this->submitDate = new \DateTime();
        if($info != null) {
            if(isset($info['po']))
                $this->orderNumber = $info['po'];
            if(isset($info['pick_up_date']))
                $this->pickUpDate = new \DateTime($info['pick_up_date']);
            if(isset($info['agent_name']))
                $this->pickUpAgent = $info['agent_name'];

            if(isset($info['pick_up']) && $info['pick_up'] == 'true')
                $this->isPickUp = 1;
            else if(isset($info['ship']) && $info['ship'] == 'true')
                $this->isPickUp = 0;

            if(isset($info['comments']))
                $this->comments = $info['comments'];
            if(isset($info['ship_name']))
                $this->shipName = $info['ship_name'];
            if(isset($info['address']))
                $this->shipAddress = $info['address'];
            if(isset($info['address2']))
                $this->shipAddress2 = $info['address2'];
            if(isset($info['city']))
                $this->ship_city = $info['city'];
            if(isset($info['zip']))
                $this->ship_zip = $info['zip'];
            if(isset($info['phone']))
                $this->shipPhone = $info['phone'];
            if(isset($info['email']))
                $this->shipEmail = $info['email'];

        }
    }

    public function getSubTotal() {
        $total = 0;

        foreach($this->getProductVariants() as $productVariant)
            $total += $productVariant->getQuantity() * $productVariant->getPrice();
        foreach($this->getPopItems() as $popItem)
            $total += $popItem->getQuantity() * $popItem->getPrice();

        return $total;
    }

    public function getTotal() {
        $total = 0;

        foreach($this->getProductVariants() as $productVariant)
            $total += $productVariant->getQuantity() * $productVariant->getPrice();
        foreach($this->getPopItems() as $popItem)
            $total += $popItem->getQuantity() * $popItem->getPrice();

        $total += $this->getShipping();
        return $total;
    }

    public function getNumItems() {
        $total = 0;

        foreach($this->getProductVariants() as $productVariant)
            $total = $total + $productVariant->getQuantity();
        foreach($this->getPopItems() as $popItem)
            $total = $total + $popItem->getQuantity();

        return $total;
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
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return Orders
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set pickUpDate
     *
     * @param \DateTime $pickUpDate
     *
     * @return Orders
     */
    public function setPickUpDate($pickUpDate)
    {
        $this->pickUpDate = $pickUpDate;

        return $this;
    }

    /**
     * Get pickUpDate
     *
     * @return \DateTime
     */
    public function getPickUpDate()
    {
        return $this->pickUpDate;
    }

    /**
     * Set pickUpAgent
     *
     * @param string $pickUpAgent
     *
     * @return Orders
     */
    public function setPickUpAgent($pickUpAgent)
    {
        $this->pickUpAgent = $pickUpAgent;

        return $this;
    }

    /**
     * Get pickUpAgent
     *
     * @return string
     */
    public function getPickUpAgent()
    {
        return $this->pickUpAgent;
    }

    /**
     * Set isPickUp
     *
     * @param boolean $isPickUp
     *
     * @return Orders
     */
    public function setIsPickUp($isPickUp)
    {
        $this->isPickUp = $isPickUp;

        return $this;
    }

    /**
     * Get isPickUp
     *
     * @return bool
     */
    public function getIsPickUp()
    {
        return $this->isPickUp;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Orders
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set shipName
     *
     * @param string $shipName
     *
     * @return Orders
     */
    public function setShipName($shipName)
    {
        $this->shipName = $shipName;

        return $this;
    }

    /**
     * Get shipName
     *
     * @return string
     */
    public function getShipName()
    {
        return $this->shipName;
    }

    /**
     * Set shipAddress
     *
     * @param string $shipAddress
     *
     * @return Orders
     */
    public function setShipAddress($shipAddress)
    {
        $this->shipAddress = $shipAddress;

        return $this;
    }

    /**
     * Get shipAddress
     *
     * @return string
     */
    public function getShipAddress()
    {
        return $this->shipAddress;
    }

    /**
     * Set shipAddress2
     *
     * @param string $shipAddress2
     *
     * @return Orders
     */
    public function setShipAddress2($shipAddress2)
    {
        $this->shipAddress2 = $shipAddress2;

        return $this;
    }

    /**
     * Get shipAddress2
     *
     * @return string
     */
    public function getShipAddress2()
    {
        return $this->shipAddress2;
    }

    /**
     * @return string
     */
    public function getShipCity()
    {
        return $this->ship_city;
    }

    /**
     * @param string $ship_city
     */
    public function setShipCity($ship_city)
    {
        $this->ship_city = $ship_city;
    }

    /**
     * @return int
     */
    public function getShipZip()
    {
        return $this->ship_zip;
    }

    /**
     * @param int $ship_zip
     */
    public function setShipZip($ship_zip)
    {
        $this->ship_zip = $ship_zip;
    }


    /**
     * Set shipPhone
     *
     * @param string $shipPhone
     *
     * @return Orders
     */
    public function setShipPhone($shipPhone)
    {
        $this->shipPhone = $shipPhone;

        return $this;
    }

    /**
     * Get shipPhone
     *
     * @return string
     */
    public function getShipPhone()
    {
        return $this->shipPhone;
    }

    /**
     * Set shipEmail
     *
     * @param string $shipEmail
     *
     * @return Orders
     */
    public function setShipEmail($shipEmail)
    {
        $this->shipEmail = $shipEmail;

        return $this;
    }

    /**
     * Get shipEmail
     *
     * @return string
     */
    public function getShipEmail()
    {
        return $this->shipEmail;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * @param \DateTime $submitDate
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }

    /**
     * @return mixed
     */
    public function getProductVariants()
    {
        return $this->product_variants;
    }

    /**
     * @param mixed $product_variants
     */
    public function setProductVariants($product_variants)
    {
        $this->product_variants = $product_variants;
    }

    public function addProductVariants(OrdersProductVariant $productVariant)
    {
        $this->product_variants[] = $productVariant;

        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount / 100;
    }

    /**
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount * 100;
    }

    /**
     * @return int
     */
    public function getShipping()
    {
        return $this->shipping / 100;
    }

    /**
     * @param int $shipping
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping * 100;
    }


    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
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
    public function getSubmittedByUser()
    {
        return $this->submitted_by_user;
    }

    /**
     * @param mixed $submitted_by_user
     */
    public function setSubmittedByUser($submitted_by_user)
    {
        $this->submitted_by_user = $submitted_by_user;
    }

    /**
     * @return mixed
     */
    public function getSubmittedForUser()
    {
        return $this->submitted_for_user;
    }

    /**
     * @param mixed $submitted_for_user
     */
    public function setSubmittedForUser($submitted_for_user)
    {
        $this->submitted_for_user = $submitted_for_user;
    }

    /**
     * @return mixed
     */
    public function getPopItems()
    {
        return $this->pop_items;
    }

    /**
     * @param mixed $pop_items
     */
    public function setPopItems($pop_items)
    {
        $this->pop_items = $pop_items;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @param string $payment_type
     */
    public function setPaymentType($payment_type)
    {
        $this->payment_type = $payment_type;
    }

    /**
     * @return int
     */
    public function getAmountPaid()
    {
        return $this->amount_paid;
    }

    /**
     * @param int $amount_paid
     */
    public function setAmountPaid($amount_paid)
    {
        $this->amount_paid = $amount_paid;
    }

    /**
     * @return string
     */
    public function getShipDescription()
    {
        return $this->ship_description;
    }

    /**
     * @param string $ship_description
     */
    public function setShipDescription($ship_description)
    {
        $this->ship_description = $ship_description;
    }

    /**
     * @return string
     */
    public function getShipCode()
    {
        return $this->ship_code;
    }

    /**
     * @param string $ship_code
     */
    public function setShipCode($ship_code)
    {
        $this->ship_code = $ship_code;
    }


}

