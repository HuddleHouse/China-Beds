<?php

namespace InventoryBundle\Repository;

use InventoryBundle\Entity\Warehouse;

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
            $image_url = '';
            foreach($prod->getImages() as $image) {
                $image_url = $image->getWebPath();
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
            $image_url = '';
            foreach($prod->getImages() as $image) {
                $image_url = $image->getWebPath();
                break;
            }

            foreach($prod->getVariants() as $variant) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id");
                $statement->bindValue('product_variant_id', $variant->getId());
                $statement->execute();
                $total_quantity = $statement->fetch();

                if(isset($warehouse)) {
                    $connection = $em->getConnection();
                    $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
                    $statement->bindValue('product_variant_id', $variant->getId());
                    $statement->bindValue('warehouse_id', $warehouse->getId());
                    $statement->execute();
                    $warehouse_quantity = $statement->fetch();
                }
                else {
                    $warehouse_quantity = 0;
                }

                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId(),
                    'image_url' => $image_url,
                    'total_quantity' => $total_quantity['total'],
                    'warehouse_quantity' => $warehouse_quantity['total'],
                    'add_quantity' => 0
                );
            }
        }

        return $products;
    }
}
