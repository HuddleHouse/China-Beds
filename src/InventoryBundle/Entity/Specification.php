<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Specification
 *
 * @ORM\Table(name="specifications")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\SpecificationRepository")
 */
class Specification
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
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\ProductSpecification", mappedBy="specification")
     */
    private $product_specifications;


    public function __construct() {
        $this->product_specifications = new ArrayCollection();
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
     * @return ProductSpecification
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
     * @return ProductSpecification
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
     * Add productSpecification
     *
     * @param \InventoryBundle\Entity\ProductSpecification $productSpecification
     *
     * @return Specification
     */
    public function addProductSpecification(\InventoryBundle\Entity\ProductSpecification $productSpecification)
    {
        $this->product_specifications[] = $productSpecification;

        return $this;
    }

    /**
     * Remove productSpecification
     *
     * @param \InventoryBundle\Entity\ProductSpecification $productSpecification
     */
    public function removeProductSpecification(\InventoryBundle\Entity\ProductSpecification $productSpecification)
    {
        $this->product_specifications->removeElement($productSpecification);
    }

    /**
     * Get productSpecifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductSpecifications()
    {
        return $this->product_specifications;
    }
}
