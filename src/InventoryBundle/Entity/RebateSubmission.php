<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RebateSubmission
 *
 * @ORM\Table(name="rebate_submission")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\RebateSubmissionRepository")
 */
class RebateSubmission
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
     * @var int
     *
     * @ORM\Column(name="rebate_id", type="integer")
     */
    private $rebateId;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_requested", type="integer", nullable=true)
     */
    private $amountRequested;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_issued", type="integer", nullable=true)
     */
    private $amountIssued;

    /**
     * @var bool
     *
     * @ORM\Column(name="credit_issued", type="boolean")
     */
    private $creditIssued;


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
     * Set rebateId
     *
     * @param integer $rebateId
     *
     * @return RebateSubmission
     */
    public function setRebateId($rebateId)
    {
        $this->rebateId = $rebateId;

        return $this;
    }

    /**
     * Get rebateId
     *
     * @return int
     */
    public function getRebateId()
    {
        return $this->rebateId;
    }

    /**
     * Set amountRequested
     *
     * @param integer $amountRequested
     *
     * @return RebateSubmission
     */
    public function setAmountRequested($amountRequested)
    {
        $this->amountRequested = $amountRequested;

        return $this;
    }

    /**
     * Get amountRequested
     *
     * @return int
     */
    public function getAmountRequested()
    {
        return $this->amountRequested;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return RebateSubmission
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set amountIssued
     *
     * @param integer $amountIssued
     *
     * @return RebateSubmission
     */
    public function setAmountIssued($amountIssued)
    {
        $this->amountIssued = $amountIssued;

        return $this;
    }

    /**
     * Get amountIssued
     *
     * @return int
     */
    public function getAmountIssued()
    {
        return $this->amountIssued;
    }

    /**
     * Set creditIssued
     *
     * @param boolean $creditIssued
     *
     * @return RebateSubmission
     */
    public function setCreditIssued($creditIssued)
    {
        $this->creditIssued = $creditIssued;

        return $this;
    }

    /**
     * Get creditIssued
     *
     * @return bool
     */
    public function getCreditIssued()
    {
        return $this->creditIssued;
    }
}

