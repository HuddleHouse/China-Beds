<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttribute
 *
 * @ORM\Table(name="product_attributes")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ProductAttributeRepository")
 */
class ProductAttribute
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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer")
     */
    private $attribute_id;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="attributes")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    private $product;

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
     * Set description
     *
     * @param string $description
     *
     * @return ProductAttribute
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

    /**
     * Get productId
     *
     * @return int
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set attributeId
     *
     * @param integer $attributeId
     *
     * @return ProductAttribute
     */
    public function setAttributeId($attributeId)
    {
        $this->attribute_id = $attributeId;

        return $this;
    }

    /**
     * Get attributeId
     *
     * @return int
     */
    public function getAttributeId()
    {
        return $this->attribute_id;
    }
}

