<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_phone", type="string", length=255)
     */
    private $shipPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="ship_email", type="string", length=255, nullable=true)
     */
    private $shipEmail;


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
     * Set city
     *
     * @param string $city
     *
     * @return Orders
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
     * Set zip
     *
     * @param integer $zip
     *
     * @return Orders
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
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
}

