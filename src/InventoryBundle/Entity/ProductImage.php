<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ProductAttribute
 *
 * @ORM\Table(name="product_images")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class ProductImage
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="images")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @ORM\Column(type="boolean")
     */
    public $detail_image = false;
    
    public function toArray() {
      
      return [
            'id'                => $this->getId(),
            'path'              => $this->getWebPath(),
            'detailImage'       => $this->getDetailImage(),
        ];
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
     * Set productId
     *
     * @param integer $productId
     *
     * @return ProductAttribute
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
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
        $tmp = __DIR__ . '/../../../web/' . $this->getUploadDir();
        return $tmp;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/products';
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
     * Get product
     *
     * @return \InventoryBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
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
     * Set detailImage
     *
     * @param boolean $detailImage
     *
     * @return ProductImage
     */
    public function setDetailImage($detailImage)
    {
        $this->detail_image = $detailImage;

        return $this;
    }

    /**
     * Get detailImage
     *
     * @return boolean
     */
    public function getDetailImage()
    {
        return $this->detail_image;
    }
}
