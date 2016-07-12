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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Rebate", inversedBy="submissions")
     * @ORM\JoinColumn(name="rebate_id", referencedColumnName="id")
     */
    private $rebate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="rebate_submissions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_requested", type="integer", nullable=true)
     */
    private $amountRequested;

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
     * @return mixed
     */
    public function getRebate()
    {
        return $this->rebate;
    }

    /**
     * @param mixed $rebate
     */
    public function setRebate($rebate)
    {
        $this->rebate = $rebate;
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

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}

