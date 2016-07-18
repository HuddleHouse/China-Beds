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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="price_group_prices")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

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
     * Set product
     *
     * @param string $product
     *
     * @return PriceGroupPrices
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
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

