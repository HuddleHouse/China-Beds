<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;
use OrderBundle\Entity\Ledger;
use OrderBundle\Entity\Orders;

/**
 * WarrantyClaim
 *
 * @ORM\Table(name="warranty_claim")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\WarrantyClaimRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WarrantyClaim
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
     * @ORM\Column(name="date_made_aware", type="datetime", nullable=true)
     */
    private $dateMadeAware;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_claim", type="datetime")
     */
    private $dateOfClaim;

    /**
     * @var int
     *
     * @ORM\Column(name="mattress_model_id", type="integer", nullable=true)
     */
    private $mattressModelId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="credit_requested", type="integer", nullable=true)
     */
    private $creditRequested;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="resolution", type="text", nullable=true)
     */
    private $resolution;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="warranty_claims")
     * @ORM\JoinColumn(name="submitted_for_user_id", referencedColumnName="id")
     */
    private $submittedForUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="submitted_warranty_claims")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id")
     */
    private $submittedByUser;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\Ledger", mappedBy="warrantyClaim")
     */
    private $ledgers;

    /**
     * @ORM\ManyToOne(targetEntity="OrderBundle\Entity\Orders", inversedBy="warranty_claims")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="warranty_claims")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $productVariant;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="warranty_claims")
     */
    private $channel;

    /**
     * @Assert\Image
     */
    private $file1;

    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    public $path1;

    /**
     * @Assert\Image
     */
    private $file2;

    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    public $path2;

    /**
     * @Assert\Image
     */
    private $file3;

    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    public $path3;

    /**
     * @ORM\Column(name="law_label", type="string", nullable=true)
     */
    private $lawLabel;

    /**
     * @ORM\Column(name="fr_label", type="string", nullable=true)
     */
    private $frLabel;

    /**
     * WarrantyClaim constructor.
     */
    public function __construct()
    {
        $this->setDateOfClaim(new \DateTime());
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
     * Set dateMadeAware
     *
     * @param \DateTime $dateMadeAware
     *
     * @return WarrantyClaim
     */
    public function setDateMadeAware($dateMadeAware)
    {
        $this->dateMadeAware = $dateMadeAware;

        return $this;
    }

    /**
     * Get dateMadeAware
     *
     * @return \DateTime
     */
    public function getDateMadeAware()
    {
        return $this->dateMadeAware;
    }

    /**
     * Set dateOfClaim
     *
     * @param \DateTime $dateOfClaim
     *
     * @return WarrantyClaim
     */
    public function setDateOfClaim($dateOfClaim)
    {
        $this->dateOfClaim = $dateOfClaim;

        return $this;
    }

    /**
     * Get dateOfClaim
     *
     * @return \DateTime
     */
    public function getDateOfClaim()
    {
        return $this->dateOfClaim;
    }

    /**
     * Set mattressModelId
     *
     * @param integer $mattressModelId
     *
     * @return WarrantyClaim
     */
    public function setMattressModelId($mattressModelId)
    {
        $this->mattressModelId = $mattressModelId;

        return $this;
    }

    /**
     * Get mattressModelId
     *
     * @return int
     */
    public function getMattressModelId()
    {
        return $this->mattressModelId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return WarrantyClaim
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set creditRequested
     *
     * @param float $creditRequested
     *
     * @return WarrantyClaim
     */
    public function setCreditRequested($creditRequested)
    {
        $this->creditRequested = $creditRequested * 100;

        return $this;
    }

    /**
     * Get creditRequested
     *
     * @return float
     */
    public function getCreditRequested()
    {
        return $this->creditRequested / 100;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WarrantyClaim
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
     * Set resolution
     *
     * @param string $resolution
     *
     * @return WarrantyClaim
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @return mixed
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * @param mixed $isArchived
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
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
     * @return ArrayCollection
     */
    public function getLedgers()
    {
        return $this->ledgers;
    }

    /**
     * @param Ledger $ledgers
     */
    public function setLedger($ledgers)
    {
        $this->ledgers = $ledgers;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Orders $order
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
     * @param mixed $productVariant
     */
    public function setProductVariant($productVariant)
    {
        $this->productVariant = $productVariant;
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
     * @return WarrantyClaim
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
    public function getFile1()
    {
        return $this->file1;
    }

    /**
     * @param mixed $file1
     */
    public function setFile1($file1)
    {
        $this->file1 = $file1;
    }

    /**
     * @return mixed
     */
    public function getPath1()
    {
        return $this->path1;
    }

    /**
     * @param mixed $path1
     */
    public function setPath1($path1)
    {
        $this->path1 = $path1;
    }

    /**
     * @return mixed
     */
    public function getFile2()
    {
        return $this->file2;
    }

    /**
     * @param mixed $file2
     */
    public function setFile2($file2)
    {
        $this->file2 = $file2;
    }

    /**
     * @return mixed
     */
    public function getPath2()
    {
        return $this->path2;
    }

    /**
     * @param mixed $path2
     */
    public function setPath2($path2)
    {
        $this->path2 = $path2;
    }

    /**
     * @return mixed
     */
    public function getFile3()
    {
        return $this->file3;
    }

    /**
     * @param mixed $file3
     */
    public function setFile3($file3)
    {
        $this->file3 = $file3;
    }

    /**
     * @return mixed
     */
    public function getPath3()
    {
        return $this->path3;
    }

    /**
     * @param mixed $path3
     */
    public function setPath3($path3)
    {
        $this->path3 = $path3;
    }

    public function upload1()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile1()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $fname = md5(rand(0,100000) . $this->getFile1()->getClientOriginalName()) . '-' . $this->getFile1()->getClientOriginalName();

        $this->getFile1()->move(
            $this->getUploadRootDir(),
            $fname
        );

        // set the path property to the filename where you've saved the file
        $this->path1 = $fname;

        // clean up the file property as you won't need it anymore
        $this->file1 = null;
    }

    public function upload2()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile2()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $fname = md5(rand(0,100000) . $this->getFile2()->getClientOriginalName()) . '-' . $this->getFile2()->getClientOriginalName();

        $this->getFile2()->move(
            $this->getUploadRootDir(),
            $fname
        );

        // set the path property to the filename where you've saved the file
        $this->path2 = $fname;

        // clean up the file property as you won't need it anymore
        $this->file2 = null;
    }

    public function upload3()
    {
        // the file property can be empty if the field is not required
        if(null === $this->getFile3()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $fname = md5(rand(0,100000) . $this->getFile3()->getClientOriginalName()) . '-' . $this->getFile3()->getClientOriginalName();

        $this->getFile3()->move(
            $this->getUploadRootDir(),
            $fname
        );

        // set the path property to the filename where you've saved the file
        $this->path3 = $fname;

        // clean up the file property as you won't need it anymore
        $this->file3 = null;
    }

    public function getAbsolutePath1()
    {
        return null === $this->path1
            ? null
            : $this->getUploadRootDir() . '/' . $this->path1;
    }

    public function getWebPath1()
    {
        return null === $this->path1
            ? null
            : $this->getUploadDir() . '/' . $this->path1;
    }

    public function getAbsolutePath2()
    {
        return null === $this->path2
            ? null
            : $this->getUploadRootDir() . '/' . $this->path2;
    }

    public function getWebPath2()
    {
        return null === $this->path2
            ? null
            : $this->getUploadDir() . '/' . $this->path2;
    }

    public function getAbsolutePath3()
    {
        return null === $this->path3
            ? null
            : $this->getUploadRootDir() . '/' . $this->path3;
    }

    public function getWebPath3()
    {
        return null === $this->path3
            ? null
            : $this->getUploadDir() . '/' . $this->path3;
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
        return 'uploads/warranty_images';
    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload()
    {
        $file_path = $this->getAbsolutePath1();
        if(file_exists($file_path)) unlink($file_path);
        $file_path = $this->getAbsolutePath2();
        if(file_exists($file_path)) unlink($file_path);
        $file_path = $this->getAbsolutePath3();
        if(file_exists($file_path)) unlink($file_path);
    }

    public function getWarrantyClaimId() {
        return sprintf('W-%s', str_pad($this->getId(), 5, 0, STR_PAD_LEFT));
    }

    /**
     * Set lawLabel
     *
     * @param string $lawLabel
     *
     * @return WarrantyClaim
     */
    public function setLawLabel($lawLabel)
    {
        $this->lawLabel = $lawLabel;

        return $this;
    }

    /**
     * Get lawLabel
     *
     * @return string
     */
    public function getLawLabel()
    {
        return $this->lawLabel;
    }

    /**
     * Set frLabel
     *
     * @param string $frLabel
     *
     * @return WarrantyClaim
     */
    public function setFrLabel($frLabel)
    {
        $this->frLabel = $frLabel;

        return $this;
    }

    /**
     * Get frLabel
     *
     * @return string
     */
    public function getFrLabel()
    {
        return $this->frLabel;
    }
}
