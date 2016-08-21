<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseOrderProductVariant
 *
 * @ORM\Table(name="purchase_order_product_variant")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\PurchaseOrderProductVariantRepository")
 */
class PurchaseOrderProductVariant
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
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\PurchaseOrder", inversedBy="product_variants")
     * @ORM\JoinColumn(name="purchase_order_id", referencedColumnName="id")
     */
    private $purchase_order;

    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ProductVariant", inversedBy="purchase_order_product_variant")
     * @ORM\JoinColumn(name="product_variant_id", referencedColumnName="id")
     */
    private $product_variant;

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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PurchaseOrderProductVariant
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
     * @return mixed
     */
    public function getPurchaseOrder()
    {
        return $this->purchase_order;
    }

    /**
     * @param mixed $purchase_order
     */
    public function setPurchaseOrder($purchase_order)
    {
        $this->purchase_order = $purchase_order;
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


}

