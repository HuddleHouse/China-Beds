<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PriceGroupPrices
 *
 * @ORM\Table(name="price_group_prices")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PriceGroupPricesRepository")
 */
class PriceGroupPrices
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
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="price_group_prices")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $product_variant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PriceGroup", inversedBy="prices")
     * @ORM\JoinColumn(name="price_group_id", referencedColumnName="id")
     */
    private $price_group;


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
     * Set price
     *
     * @param integer $price
     *
     * @return PriceGroupPrices
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getProductVariant()
    {
        return $this->product_variant;
    }

    /**
     * @param mixed $product_variant
     */
    public function setProductVariant($product_variant)
    {
        $this->product_variant = $product_variant;
    }

    /**
     * @return mixed
     */
    public function getPriceGroup()
    {
        return $this->price_group;
    }

    /**
     * @param mixed $price_group
     */
    public function setPriceGroup($price_group)
    {
        $this->price_group = $price_group;
    }


}
