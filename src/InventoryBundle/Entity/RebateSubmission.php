<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * RebateSubmission
 *
 * @ORM\Table(name="rebate_submission")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\RebateSubmissionRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_posted", type="datetime", nullable=true)
     */
    private $datePosted;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Rebate", inversedBy="submissions")
     * @ORM\JoinColumn(name="rebate_id", referencedColumnName="id")
     */
    private $rebate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="rebate_submissions")
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submittedForUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="submitted_rebates")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_requested", type="integer", nullable=true)
     */
    private $amountRequested = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_issued", type="integer", nullable=true)
     */
    private $amountIssued;

    /**
     * @var bool
     *
     * @ORM\Column(name="credit_issued", type="boolean", nullable=true)
     */
    private $creditIssued;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="rebateSubmission")
     */
    private $ledgers;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="rebate_submissions")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="rebate_submissions")
     */
    private $channel;

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
     * @ORM\Column(type="text", nullable=true)
     */
    public $description;

    /**
     * RebateSubmission constructor.
     */
    public function __construct()
    {
        $this->setDateCreated(new \DateTime());
        $this->ledgers = new ArrayCollection();
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
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getDatePosted()
    {
        return $this->datePosted;
    }

    /**
     * @param mixed $datePosted
     */
    public function setDatePosted($datePosted)
    {
        $this->datePosted = $datePosted;
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
        $this->amountRequested = $amountRequested*100;

        return $this;
    }

    /**
     * Get amountRequested
     *
     * @return int
     */
    public function getAmountRequested()
    {
        return $this->amountRequested/100;
    }

    /**
     * @return int
     */
    public function getAmountIssued()
    {
        return $this->amountIssued/100;
    }

    /**
     * @param int $amountIssued
     */
    public function setAmountIssued($amountIssued)
    {
        $this->amountIssued = $amountIssued*100;
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
        return 'uploads/rebate_submissions';
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
     * @return mixed
     */
    public function getSubmittedForUser()
    {
        return $this->submittedForUser;
    }

    /**
     * @param mixed $submittedForUser
     */
    public function setSubmittedForUser($submittedForUser)
    {
        $this->submittedForUser = $submittedForUser;
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
    public function getLedgers()
    {
        return $this->ledgers;
    }

    /**
     * @param mixed $ledgers
     */
    public function setLedgers($ledgers)
    {
        $this->ledgers = $ledgers;
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
     * Add ledger
     *
     * @param \OrderBundle\Entity\Ledger $ledger
     *
     * @return RebateSubmission
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
}
