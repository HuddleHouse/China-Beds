<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="tagline", type="string", length=255, nullable=true)
     */
    private $tagline;

    /**
     * @var string
     *
     * @ORM\Column(name="front_headline", type="string", length=255, nullable=true)
     */
    private $front_headline;

    /**
     * @var string
     *
     * @ORM\Column(name="list_id", type="string", length=255, nullable=true)
     */
    private $list_id;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="promo_kit_available", type="boolean")
     */
    private $promo_kit_available = false;

    /**
     * @var bool
     * @ORM\Column(name="hide_frontend", type="boolean")
     */
    private $hideFrontend = 0;

    /**
     * @var bool
     *@ORM\Column(name="hide_backend", type="boolean")
     */
    private $hideBackend = 0;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductAttribute", mappedBy="product", cascade={"persist", "remove"})
     */
    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductCategory", mappedBy="product", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductVariant", mappedBy="product", cascade={"persist", "remove"})
     */
    private $variants;
    
    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductChannel", mappedBy="product", cascade={"persist", "remove"})
     */
    private $channels;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductSpecification", mappedBy="product", cascade={"persist", "remove"})
     */
    private $specifications;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductImage", mappedBy="product", cascade={"persist", "remove"})
     */
    private $images;


    public function __construct() {
        $this->attributes = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->specifications = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->variants = new ArrayCollection();
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
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * @return string
     */
    public function getFrontHeadline()
    {
        return $this->front_headline;
    }

    /**
     * @param string $front_headline
     */
    public function setFrontHeadline($front_headline)
    {
        $this->front_headline = $front_headline;
    }

    
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Produsrc/InventoryBundle/Repository/ChannelRepository.php:20
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
     * @return string
     */
    public function getTagline()
    {
        return $this->tagline;
    }

    /**
     * @param string $tagline
     */
    public function setTagline($tagline)
    {
        $this->tagline = $tagline;
    }

    
    
    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return Product
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return Product
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
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
     * @return boolean
     */
    public function isPromoKitAvailable()
    {
        return $this->promo_kit_available;
    }

    /**
     * @param boolean $promo_kit_available
     */
    public function setPromoKitAvailable($promo_kit_available)
    {
        $this->promo_kit_available = $promo_kit_available;
    }

    /**
     * @return mixed
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param mixed $channels
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
    }

    /**
     * @return mixed
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * @param mixed $specifications
     */
    public function setSpecifications($specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * @return mixed
     */
    public function getImages($public_only = false)
    {
        $images = [];

        foreach($this->images as $image) {
            if ( !$public_only || ($public_only && !$image->getDetailImage()) ) {
                $images[] = $image;
            }
        }
        return $images;
    }

    public function getDetailImage() {
        foreach($this->images as $image) {
            if ( $image->getDetailImage() ) {
                return $image;
            }
        }
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return string
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * @param string $list_id
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;
    }

    /**
     * @return mixed
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param mixed $variants
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
    }

    /**
     * Add attribute
     *
     * @param \InventoryBundle\Entity\ProductAttribute $attribute
     *
     * @return Product
     */
    public function addAttribute(\InventoryBundle\Entity\ProductAttribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param \InventoryBundle\Entity\ProductAttribute $attribute
     */
    public function removeAttribute(\InventoryBundle\Entity\ProductAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * Add category
     *
     * @param \InventoryBundle\Entity\ProductCategory $category
     *
     * @return Product
     */
    public function addCategory(\InventoryBundle\Entity\ProductCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \InventoryBundle\Entity\ProductCategory $category
     */
    public function removeCategory(\InventoryBundle\Entity\ProductCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Add variant
     *
     * @param \InventoryBundle\Entity\ProductVariant $variant
     *
     * @return Product
     */
    public function addVariant(\InventoryBundle\Entity\ProductVariant $variant)
    {
        $this->variants[] = $variant;

        return $this;
    }

    /**
     * Remove variant
     *
     * @param \InventoryBundle\Entity\ProductVariant $variant
     */
    public function removeVariant(\InventoryBundle\Entity\ProductVariant $variant)
    {
        $this->variants->removeElement($variant);
    }

    /**
     * Add channel
     *
     * @param \InventoryBundle\Entity\ProductChannel $channel
     *
     * @return Product
     */
    public function addChannel(\InventoryBundle\Entity\ProductChannel $channel)
    {
        $channel->setProduct($this);
        $this->channels[] = $channel;

        return $this;
    }

    /**
     * Remove channel
     *
     * @param \InventoryBundle\Entity\ProductChannel $channel
     */
    public function removeChannel(\InventoryBundle\Entity\ProductChannel $channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * Add specification
     *
     * @param \InventoryBundle\Entity\ProductSpecification $specification
     *
     * @return Product
     */
    public function addSpecification(\InventoryBundle\Entity\ProductSpecification $specification)
    {
        $this->specifications[] = $specification;

        return $this;
    }

    /**
     * Remove specification
     *
     * @param \InventoryBundle\Entity\ProductSpecification $specification
     */
    public function removeSpecification(\InventoryBundle\Entity\ProductSpecification $specification)
    {
        $this->specifications->removeElement($specification);
    }

    /**
     * Add image
     *
     * @param \InventoryBundle\Entity\ProductImage $image
     *
     * @return Product
     */
    public function addImage(\InventoryBundle\Entity\ProductImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \InventoryBundle\Entity\ProductImage $image
     */
    public function removeImage(\InventoryBundle\Entity\ProductImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Set hideFrontend
     *
     * @param boolean $hideFrontend
     *
     * @return Product
     */
    public function setHideFrontend($hideFrontend)
    {
        $this->hideFrontend = $hideFrontend;

        return $this;
    }

    /**
     * Get hideFrontend
     *
     * @return boolean
     */
    public function getHideFrontend()
    {
        return $this->hideFrontend;
    }

    /**
     * Get promoKitAvailable
     *
     * @return boolean
     */
    public function getPromoKitAvailable()
    {
        return $this->promo_kit_available;
    }

    /**
     * Set hideBackend
     *
     * @param boolean $hideBackend
     *
     * @return Product
     */
    public function setHideBackend($hideBackend)
    {
        $this->hideBackend = $hideBackend;

        return $this;
    }

    /**
     * Get hideBackend
     *
     * @return boolean
     */
    public function getHideBackend()
    {
        return $this->hideBackend;
    }

    public function toArray() {
        $categories = [];
        foreach($this->getCategories() as $category) {
            $categories[] = $category->getCategory()->toArray();
        }
        $images = [];
        foreach($this->getImages() as $image) {
            $images[] = $image->getWebPath();
        }
        return [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'description'   => $this->getDescription(),
            'hide_backend'  => $this->getHideBackend(),
            'categories'    => $categories,
            'images'        => $images
        ];
    }
}
