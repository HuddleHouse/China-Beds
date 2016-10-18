<?php

namespace InventoryBundle\Repository;

use InventoryBundle\Entity\Channel;
use InventoryBundle\Entity\Product;
use WarehouseBundle\Entity\Warehouse;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Returns a formatted array with all products
     *
     * @return array
     */
    public function getAllProductsArray()
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $image_url = '/';
            foreach($prod->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            foreach($prod->getVariants() as $variant)
                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId(),
                    'image_url' => $image_url
                );
        }

        return $products;
    }

    /**
     * Returns single product based on variant
     *
     *
     * @return Product
     */
    public function getProdImg($variantId){
        $em = $this->getEntityManager();
        $productVariant = $em->getRepository('InventoryBundle:ProductVariant')->find($variantId);
        $product = $productVariant->getProduct();

        $image_url = '/';

        foreach($product->getImages() as $image) {
            $image_url .= $image->getWebPath();
            break;
        }

        return $image_url;
    }

    public function getAllMattressVariantsForChannelArray(Channel $channel)
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $is_channel = $is_mattress = 0;

            foreach($prod->getChannels() as $chan)
                if($chan->getId() == $channel->getId())
                    $is_channel = 1;
            foreach($prod->getCategories() as $category)
                if($category->getname() == "Mattresses")
                    $is_mattress = 1;

            if($is_mattress == 1 && $is_channel == 1) {
                $image_url = '/';
                foreach($prod->getImages() as $image) {
                    $image_url .= $image->getWebPath();
                    break;
                }

                foreach($prod->getVariants() as $variant)
                    $products[] = array(
                        'name' => $prod->getName() . ": " . $variant->getName(),
                        'id' => $variant->getId(),
                        'image_url' => $image_url
                    );
            }
        }
        return $products;
    }


    public function getAllMattressesForChannelArray(Channel $channel)
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $is_channel = $is_mattress = 0;

            foreach($prod->getChannels() as $chan)
                if($chan->getChannel()->getId() == $channel->getId())
                    $is_channel = 1;
            foreach($prod->getCategories() as $category)
                if($category->getCategory()->getName() == "Mattresses")
                    $is_mattress = 1;

            if($is_mattress == 1 && $is_channel == 1 && $prod->getActive() == true) {
                $image_url = '/';
                foreach($prod->getImages() as $image) {
                    $image_url .= $image->getWebPath();
                    break;
                }
                $lowest_price = 9999999;

                foreach($prod->getVariants() as $variant)
                    if($variant->getMsrp() < $lowest_price)
                        $lowest_price = $variant->getMsrp();
                $products[] = array(
                    'name' => $prod->getName(),
                    'description' => $prod->getDescription(),
                    'id' => $prod->getId(),
                    'image_url' => $image_url,
                    'lowest_price' => $lowest_price
                );
            }
        }
        return $products;
    }

    public function getAllPillowsForChannelArray(Channel $channel)
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $is_channel = $is_mattress = 0;

            foreach($prod->getChannels() as $chan)
                if($chan->getChannel()->getId() == $channel->getId())
                    $is_channel = 1;
            foreach($prod->getCategories() as $category)
                if($category->getCategory()->getName() == "Pillows")
                    $is_mattress = 1;

            if($is_mattress == 1 && $is_channel == 1 && $prod->getActive() == true) {
                $image_url = '/';
                foreach($prod->getImages() as $image) {
                    $image_url .= $image->getWebPath();
                    break;
                }
                $lowest_price = 9999999;


                $products[] = array(
                    'name' => $prod->getName(),
                    'description' => $prod->getDescription(),
                    'id' => $prod->getId(),
                    'image_url' => $image_url,
                    'lowest_price' => $lowest_price
                );
            }
        }
        return $products;
    }

    public function getAllAdjustablesForChannelArray(Channel $channel)
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $is_channel = $is_mattress = 0;

            foreach($prod->getChannels() as $chan)
                if($chan->getChannel()->getId() == $channel->getId())
                    $is_channel = 1;
            foreach($prod->getCategories() as $category)
                if($category->getCategory()->getName() == "Adjustables")
                    $is_mattress = 1;

            if($is_mattress == 1 && $is_channel == 1 && $prod->getActive() == true) {
                $image_url = '/';
                foreach($prod->getImages() as $image) {
                    $image_url .= $image->getWebPath();
                    break;
                }
                $lowest_price = 9999999;


                $products[] = array(
                    'name' => $prod->getName(),
                    'description' => $prod->getDescription(),
                    'id' => $prod->getId(),
                    'image_url' => $image_url,
                    'lowest_price' => $lowest_price
                );
            }
        }
        return $products;
    }

    /**
     * Returns a formatted array with all products
     *
     * @param Warehouse $warehouse
     * @return array
     */
    public function getAllProductsWithQuantityArray(Warehouse $warehouse = null)
    {
        $em = $this->getEntityManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $image_url = '/';
            foreach($prod->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            foreach($prod->getVariants() as $variant) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id");
                $statement->bindValue('product_variant_id', $variant->getId());
                $statement->execute();
                $total_quantity = $statement->fetch();

                if(isset($warehouse)) {
                    $warehouse_quantity = $em->getRepository('WarehouseBundle:Warehouse')->getInventoryForProduct($variant, $warehouse);
                }
                else {
                    $warehouse_quantity = 0;
                }

                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId(),
                    'image_url' => $image_url,
                    'total_quantity' => $total_quantity['total'],
                    'warehouse_quantity' => $warehouse_quantity,
                    'departing_warehouse_quantity' => 0,
                    'receiving_warehouse_quantity' => 0,
                    'ordered_quantity' => 0,
                    'quantity' => 0
                );
            }
        }

        return $products;
    }
}
