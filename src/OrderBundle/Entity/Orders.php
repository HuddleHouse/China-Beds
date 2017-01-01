<?php

namespace OrderBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use WarehouseBundle\Entity\Status;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrdersRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Orders
{
    const STATUS_ACTIVE             = 'Active';
    const STATUS_RECEIVED           = 'Received';
    const STATUS_COMPLETED          = 'Completed';
    const STATUS_VOIDED             = 'Voided';
    const STATUS_DRAFT              = 'Draft';
    const STATUS_ARCHIVED           = 'Archived';
    const STATUS_PAID               = 'Paid';
    const STATUS_SHIPPED            = 'Shipped';
    const STATUS_READY_TO_SHIP      = 'Ready to Ship';
    const STATUS_PENDING            = 'Pending';
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
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255, nullable=true)
     */
    private $orderId;


    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\Status", inversedBy="stock_adjustments")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;
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
     * @var bool
     *
     * @ORM\Column(name="is_manual", type="boolean")
     */
    private $isManual = false;
    /**
     * @var bool
     *
     * @ORM\Column(name="is_paid", type="boolean")
     */
    private $isPaid = false;
    /**
     * @var bool
     *
     * @ORM\Column(name="is_shippable", type="boolean")
     */
    private $isShippable = false;
    /**
     * @var bool
     *
     * @ORM\Column(name="admin_approved", type="boolean")
     */
    private $adminApproved = false;

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
     * @var int
     *
     * @ORM\Column(name="estimated_shipping", type="integer", nullable=true)
     */
    private $estimatedShipping;

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
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersProductVariant", mappedBy="order", cascade={"persist", "remove"})
     */
    private $product_variants;

//    /**
//     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersShippingLabel", mappedBy="order", cascade={"persist"})
//     */
//    private $shipping_labels;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersPopItem", mappedBy="order", cascade={"persist", "remove"})
     */
    private $pop_items;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrdersManualItem", mappedBy="order", cascade={"persist", "remove"})
     */
    private $manual_items;

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
    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="order", cascade={"persist"})
     */
    private $ledgers;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\WarrantyClaim", mappedBy="order")
     */
    private $warranty_claims;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\RebateSubmission", mappedBy="order")
     */
    private $rebate_submissions;

    /**
     * @ORM\OneToMany(targetEntity="OrderPayment", mappedBy="order", cascade={"persist"})
     */
    private $order_payments;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\CreditRequest", mappedBy="order")
     */
    private $creditRequest;

    /**
     * @Assert\File(
     *     maxSize="6M",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    public $path;

    /**
     * Orders constructor.
     * @param null $info
     */
    public function __construct($info = null)
    {
        $this->product_variants = new ArrayCollection();
        $this->pop_items = new ArrayCollection();
        $this->manual_items = new ArrayCollection();
        $this->shipping_labels = new ArrayCollection();
        $this->warranty_claims = new ArrayCollection();
        $this->rebate_submissions = new ArrayCollection();
        $this->ledgers = new ArrayCollection();
        $this->creditRequest = new ArrayCollection();
        $this->order_payments = new ArrayCollection();
        $this->submitDate = new \DateTime();
//        $this->orderId = time().rand(1000,9999);
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
        foreach($this->manual_items as $manual_item) {
            $total += $manual_item->getPrice();
        }

        return $total;
    }

    public function getTotal() {
        $total = 0;

        foreach($this->getProductVariants() as $productVariant)
            $total += $productVariant->getQuantity() * $productVariant->getPrice();
        foreach($this->getPopItems() as $popItem)
            $total += $popItem->getQuantity() * $popItem->getPrice();
        foreach($this->manual_items as $manual_item)
            $total += $manual_item->getPrice();

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
     * @return int
     */
    public function getEstimatedShipping()
    {
        return $this->estimatedShipping / 100;
    }

    /**
     * @param int $estimatedShipping
     */
    public function setEstimatedShipping($estimatedShipping)
    {
        $this->estimatedShipping = $estimatedShipping * 100;
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
        if ( $this->isShippable && !$this->isPaid ) {
            $status = new Status();
            $status->setName('Due');
            $status->setColor('red');
            return $status;
        }
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
     * @return ArrayCollection
     */
    public function getProductVariants()
    {
        return $this->product_variants;
    }

    /**
     * @param ArrayCollection $product_variants
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
     * @return User
     */
    public function getSubmittedByUser()
    {
        return $this->submitted_by_user;
    }

    /**
     * @param User $submitted_by_user
     */
    public function setSubmittedByUser($submitted_by_user)
    {
        $this->submitted_by_user = $submitted_by_user;
    }

    /**
     * @return User
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
     * @return ArrayCollection
     */
    public function getPopItems()
    {
        return $this->pop_items;
    }

    /**
     * @param ArrayCollection $pop_items
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
        return $this->amount_paid / 100;
    }

    /**
     * @return mixed
     */
    public function getShippingLabels()
    {
        $shipping_labels = new ArrayCollection();
        foreach($this->getProductVariants() as $variant) {
            foreach($variant->getWarehouseInfo() as $info) {
                foreach($info->getShippingLabels() as $label) {
                    $shipping_labels->add($label);
                }
            }
        }
        return $shipping_labels;
    }

    /**
     * @param int $amount_paid
     */
    public function setAmountPaid($amount_paid)
    {
        $this->amount_paid = $amount_paid * 100;
    }

    /**
     * @return string
     */
    public function getShipDescription()
    {
        return $this->ship_description;
    }
    /**
     * @return ArrayCollection
     */
    public function getWarrantyClaims()
    {
        return $this->warranty_claims;
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
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getManualItems()
    {
        return $this->manual_items;
    }

    /**
     * @param mixed $manual_items
     */
    public function setManualItems($manual_items)
    {
        $this->manual_items = $manual_items;
    }



    /**
     * Add productVariant
     *
     * @param \OrderBundle\Entity\OrdersProductVariant $productVariant
     *
     * @return Orders
     */
    public function addProductVariant(\OrderBundle\Entity\OrdersProductVariant $productVariant)
    {
        $productVariant->setOrder($this);
        $this->product_variants[] = $productVariant;

        return $this;
    }

    /**
     * Remove productVariant
     *
     * @param \OrderBundle\Entity\OrdersProductVariant $productVariant
     */
    public function removeProductVariant(\OrderBundle\Entity\OrdersProductVariant $productVariant)
    {
        $this->product_variants->removeElement($productVariant);
    }

    /**
     * Remove shippingLabel
     *
     * @param \OrderBundle\Entity\OrdersShippingLabel $shippingLabel
     */
    public function removeShippingLabel(\OrderBundle\Entity\OrdersShippingLabel $shippingLabel)
    {
        $this->shipping_labels->removeElement($shippingLabel);
    }

    /**
     * Add popItem
     *
     * @param \OrderBundle\Entity\OrdersPopItem $popItem
     *
     * @return Orders
     */
    public function addPopItem(\OrderBundle\Entity\OrdersPopItem $popItem)
    {
        $this->pop_items[] = $popItem;

        return $this;
    }

    /**
     * Remove popItem
     *
     * @param \OrderBundle\Entity\OrdersPopItem $popItem
     */
    public function removePopItem(\OrderBundle\Entity\OrdersPopItem $popItem)
    {
        $this->pop_items->removeElement($popItem);
    }

    /**
     * Add manualItem
     *
     * @param \OrderBundle\Entity\OrdersManualItem $manualItem
     *
     * @return Orders
     */
    public function addManualItem(\OrderBundle\Entity\OrdersManualItem $manualItem)
    {
        $this->manual_items[] = $manualItem;

        return $this;
    }

    /**
     * Remove manualItem
     *
     * @param \OrderBundle\Entity\OrdersManualItem $manualItem
     */
    public function removeManualItem(\OrderBundle\Entity\OrdersManualItem $manualItem)
    {
        $this->manual_items->removeElement($manualItem);
    }

    /**
     * Add ledger
     *
     * @param \OrderBundle\Entity\Ledger $ledger
     *
     * @return Orders
     */
    public function addLedger(\OrderBundle\Entity\Ledger $ledger)
    {
        $ledger->setOrder($this);
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
     * @return Orders
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
     * Add rebateSubmission
     *
     * @param \InventoryBundle\Entity\RebateSubmission $rebateSubmission
     *
     * @return Orders
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
     * Add orderPayment
     *
     * @param \OrderBundle\Entity\OrderPayment $orderPayment
     *
     * @return Orders
     */
    public function addOrderPayment(\OrderBundle\Entity\OrderPayment $orderPayment)
    {
        $orderPayment->setOrder($this);
        $this->order_payments[] = $orderPayment;

        return $this;
    }

    /**
     * Remove orderPayment
     *
     * @param \OrderBundle\Entity\OrderPayment $orderPayment
     */
    public function removeOrderPayment(\OrderBundle\Entity\OrderPayment $orderPayment)
    {
        $this->order_payments->removeElement($orderPayment);
    }

    /**
     * Get orderPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderPayments()
    {
        return $this->order_payments;
    }

    public function getPaidTotal() {
        $total = 0;
        foreach($this->getOrderPayments() as $payment) {
            $total += $payment->getAmount();
        }
        return $total;
    }

    public function getItemTotal() {
        $total = 0;
        foreach($this->getProductVariants() as $variant) {
            $total += $variant->getTotal();
        }
        foreach($this->getPopItems() as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function getOrderTotal() {
        return $this->getItemTotal() + $this->getShipping();
    }

    public function getBalance() {
        return $this->getOrderTotal() - $this->getPaidTotal();
    }

    /**
     * @return boolean
     */
    public function isManual()
    {
        return $this->isManual;
    }

    /**
     * @param boolean $isManual
     */
    public function setIsManual($isManual)
    {
        $this->isManual = $isManual;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $fname = md5(rand(0,100000) . $this->getFile()->getClientOriginalName()) . '-' . $this->getFile()->getClientOriginalName();

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $fname
        );

        // set the path property to the filename where you've saved the file
        $this->path = $fname;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $tmp = __DIR__ . '/../../../web/' . $this->getUploadDir();
        return $tmp;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/manual_order_pdfs';
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload()
    {
        $file_path = $this->getAbsolutePath();
        if(file_exists($file_path)) unlink($file_path);
    }

    /**
     * @ORM\PrePersist
     */
    public function setPrePersist() {
        $this->setCreatedOn(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setFlags() {
//        if ( $this->getBalance() == 0 ) {
//            $this->setIsPaid(true);
//        }
//        if ( $this->getBalance() == 0 || $this->getIsManual() ) {
//            $this->setIsShippable(true);
//        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }



    /**
     * Get isManual
     *
     * @return boolean
     */
    public function getIsManual()
    {
        return $this->isManual;
    }

    /**
     * Add creditRequest
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequest
     *
     * @return Orders
     */
    public function addCreditRequest(\OrderBundle\Entity\CreditRequest $creditRequest)
    {
        $this->creditRequest[] = $creditRequest;

        return $this;
    }

    /**
     * Remove creditRequest
     *
     * @param \OrderBundle\Entity\CreditRequest $creditRequest
     */
    public function removeCreditRequest(\OrderBundle\Entity\CreditRequest $creditRequest)
    {
        $this->creditRequest->removeElement($creditRequest);
    }

    /**
     * Get creditRequest
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditRequest()
    {
        return $this->creditRequest;
    }

    public function getIdentifier() {
        return sprintf('O-%s', str_pad($this->getId(), 5, 0, STR_PAD_LEFT));
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     *
     * @return Orders
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * Set isShippable
     *
     * @param boolean $isShippable
     *
     * @return Orders
     */
    public function setIsShippable($isShippable)
    {
        $this->isShippable = $isShippable;

        return $this;
    }

    /**
     * Get isShippable
     *
     * @return boolean
     */
    public function getIsShippable()
    {
        return $this->isShippable;
    }

    /**
     * @return bool
     */
    public function isAdminApproved()
    {
        return $this->adminApproved;
    }

    /**
     * @param bool $adminApproved
     */
    public function setAdminApproved($adminApproved)
    {
        $this->adminApproved = $adminApproved;
    }

}
