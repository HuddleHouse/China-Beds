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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Attribute", inversedBy="attributes")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="attributes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
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

