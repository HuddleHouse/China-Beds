<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity()
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductCategory", mappedBy="category")
     */
    private $product_categories;


    public function __construct() {
        $this->product_categories = new ArrayCollection();
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
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setCode(preg_replace("/[^a-zA-Z0-9]+/", "", $name));

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
     * Set code
     *
     * @param string $code
     *
     * @return Category
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getProductCategories()
    {
        return $this->product_categories;
    }

    /**
     * @param mixed $product_categories
     */
    public function setProductCategories($product_categories)
    {
        $this->product_categories = $product_categories;
    }


    /**
     * Add productCategory
     *
     * @param \InventoryBundle\Entity\ProductCategory $productCategory
     *
     * @return Category
     */
    public function addProductCategory(\InventoryBundle\Entity\ProductCategory $productCategory)
    {
        $this->product_categories[] = $productCategory;

        return $this;
    }

    /**
     * Remove productCategory
     *
     * @param \InventoryBundle\Entity\ProductCategory $productCategory
     */
    public function removeProductCategory(\InventoryBundle\Entity\ProductCategory $productCategory)
    {
        $this->product_categories->removeElement($productCategory);
    }
}
