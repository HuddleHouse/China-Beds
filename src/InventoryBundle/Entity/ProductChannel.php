<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttribute
 *
 * @ORM\Table(name="product_channels")
 * @ORM\Entity()
 */
class ProductChannel
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
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="product_channels")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="channels")
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
    
}
