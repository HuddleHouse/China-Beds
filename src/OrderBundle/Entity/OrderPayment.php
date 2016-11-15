<?php

namespace OrderBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Orders
 *
 * @ORM\Table(name="orders_payments")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrdersRepository")
 */
class OrderPayment
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
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $method;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount = 0;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $gateway_auth_code;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $gateway_transaction_id;

    /**
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="order_payments")
     */
    private $order;

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
     * Set method
     *
     * @param string $method
     *
     * @return OrderPayment
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return OrderPayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount * 100;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount / 100;
    }

    /**
     * Set gatewayAuthCode
     *
     * @param string $gatewayAuthCode
     *
     * @return OrderPayment
     */
    public function setGatewayAuthCode($gatewayAuthCode)
    {
        $this->gateway_auth_code = $gatewayAuthCode;

        return $this;
    }

    /**
     * Get gatewayAuthCode
     *
     * @return string
     */
    public function getGatewayAuthCode()
    {
        return $this->gateway_auth_code;
    }

    /**
     * Set gatewayTransactionId
     *
     * @param string $gatewayTransactionId
     *
     * @return OrderPayment
     */
    public function setGatewayTransactionId($gatewayTransactionId)
    {
        $this->gateway_transaction_id = $gatewayTransactionId;

        return $this;
    }

    /**
     * Get gatewayTransactionId
     *
     * @return string
     */
    public function getGatewayTransactionId()
    {
        return $this->gateway_transaction_id;
    }

    /**
     * Set order
     *
     * @param \OrderBundle\Entity\Orders $order
     *
     * @return OrderPayment
     */
    public function setOrder(\OrderBundle\Entity\Orders $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \OrderBundle\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set detail
     *
     * @param string $detail
     *
     * @return OrderPayment
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
