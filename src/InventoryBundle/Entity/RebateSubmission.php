<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    private $amountRequested = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_issued", type="integer", nullable=true)
     */
    private $amountIssued = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="credit_issued", type="boolean", nullable=true)
     */
    private $creditIssued = 0;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

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
     * Set amountIssued
     *
     * @param integer $amountIssued
     *
     * @return RebateSubmission
     */
    public function setAmountIssued($amountIssued)
    {
        $this->amountIssued = $amountIssued*100;

        return $this;
    }

    /**
     * Get amountIssued
     *
     * @return int
     */
    public function getAmountIssued()
    {
        return $this->amountIssued/100;
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
}

