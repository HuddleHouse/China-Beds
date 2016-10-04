<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rebate
 *
 * @ORM\Table(name="rebate")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\RebateRepository")
 */
class Rebate
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="rebates")
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
     * @ORM\Column(name="amount_requested", type="integer")
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
     * @ORM\Column(name="credit_issued", type="boolean", nullable=true)
     */
    private $creditIssued = false;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="rebate")
     */
    private $ledgers;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="rebates")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="rebates")
     */
    private $channel;

    /**
     * Rebate constructor.
     */
    public function __construct()
    {
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
     * Set name
     *
     * @param string $name
     *
     * @return Rebate
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
     * Set description
     *
     * @param string $description
     *
     * @return Rebate
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Rebate
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set amountRequested
     *
     * @param float $amountRequested
     *
     * @return RebateSubmission
     */
    public function setAmountRequested($amountRequested)
    {
        $this->amountRequested = $amountRequested * 100;

        return $this;
    }

    /**
     * Get amountRequested
     *
     * @return float
     */
    public function getAmountRequested()
    {
        return $this->amountRequested / 100;
    }

    /**
     * Set amountIssued
     *
     * @param float $amountIssued
     *
     * @return RebateSubmission
     */
    public function setAmountIssued($amountIssued)
    {
        $this->amountIssued = $amountIssued * 100;

        return $this;
    }

    /**
     * Get amountIssued
     *
     * @return float
     */
    public function getAmountIssued()
    {
        return $this->amountIssued / 100;
    }

    /**
     * Set creditIssued
     *
     * @param boolean $creditIssued
     *
     * @return Rebate
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
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

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

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $tmp = __DIR__ . '/../../../../resume/web/' . $this->getUploadDir();
        return $tmp;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
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
    public function setLedger($ledgers)
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
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }
}

