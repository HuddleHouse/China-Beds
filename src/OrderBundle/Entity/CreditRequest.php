<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CreditRequest
 *
 * @ORM\Table(name="credit_request")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class CreditRequest
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
     * @var DateTime
     *
     * @ORM\Column(name="submit_date", type="datetime", nullable=true)
     */
    private $submitDate;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="creditRequestFor")
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submittedForUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="creditRequestBy")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="creditRequest")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $productVariant;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="creditRequest")
     */
    private $channel;

    /**
     * @var float
     *
     * @ORM\Column(name="request_amount", type="integer")
     */
    private $requestAmount;
    /**
     * @var float
     *
     * @ORM\Column(name="applied_amount", type="integer")
     */
    private $appliedAmount = 0;


    /**
     * @var bool
     *
     * @ORM\Column(name="is_approved", type="boolean")
     */
    private $isApproved = false;

    /**
     * @ORM\PrePersist
     */
    public function setDate(){
        $this->setSubmitDate(new \DateTime());
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
     * @return \AppBundle\Entity\User
     */
    public function getSubmittedForUser()
    {
        return $this->submittedForUser;
    }

    /**
     * @param \AppBundle\Entity\User $submittedForUser
     */
    public function setSubmittedForUser($submittedForUser)
    {
        $this->submittedForUser = $submittedForUser;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getSubmittedByUser()
    {
        return $this->submittedByUser;
    }

    /**
     * @param \AppBundle\Entity\User $submittedByUser
     */
    public function setSubmittedByUser($submittedByUser)
    {
        $this->submittedByUser = $submittedByUser;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * @param mixed $product
     */
    public function setProductVariant($productVariant)
    {
        $this->productVariant = $productVariant;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
     * @return DateTime
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * @param DateTime $submitDate
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }

    /**
     * @return mixed
     */
    public function getRequestAmount()
    {
        return $this->requestAmount/100;
    }

    /**
     * @param mixed $requestAmount
     */
    public function setRequestAmount($requestAmount)
    {
        $this->requestAmount = $requestAmount*100;
    }



    /**
     * Set isApproved
     *
     * @param boolean $isApproved
     *
     * @return CreditRequest
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get isApproved
     *
     * @return boolean
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set appliedAmount
     *
     * @param integer $appliedAmount
     *
     * @return CreditRequest
     */
    public function setAppliedAmount($appliedAmount)
    {
        $this->appliedAmount = $appliedAmount * 100;

        return $this;
    }

    /**
     * Get appliedAmount
     *
     * @return integer
     */
    public function getAppliedAmount()
    {
        return $this->appliedAmount/100;
    }

    public function getIdentifier() {
        return sprintf('CR-%s', str_pad($this->getId(), 5, 0, STR_PAD_LEFT));
    }
}
