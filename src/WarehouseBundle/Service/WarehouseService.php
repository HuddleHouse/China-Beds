<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 11/15/16
 * Time: 1:13 AM
 */

namespace WarehouseBundle\Service;


use AppBundle\Services\BaseService;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\ProductVariant;
use OrderBundle\Entity\Orders;
use WarehouseBundle\Entity\Warehouse;
use WarehouseBundle\Entity\WarehouseInventory;

class WarehouseService extends BaseService
{
    public function modifyInventoryLevelForOrder(Orders $order) {

        foreach($order->getProductVariants() as $variant) {
            foreach($variant->getWarehouseInfo() as $warehouse_info) {
                $this->modifyInventoryLevel($warehouse_info->getWarehouse(), $variant->getProductVariant(), $warehouse_info->getQuantity()*-1);
            }
        }

    }

    private function getWarehouseInventoryByWarehouse(Warehouse $warehouse, ProductVariant $productVariant) {
        if ( $result = $this->container->get('doctrine')->getRepository('WarehouseBundle:WarehouseInventory')->findOneBy(['warehouse' => $warehouse, 'product_variant' => $productVariant]) ) {
            return $result;
        } else {
            $result = new WarehouseInventory();
            $result->setQuantity(0);
            $result->setWarehouse($warehouse);
            $result->setProductVariant($productVariant);
            return $result;
        }
    }

    public function modifyInventoryLevel(Warehouse $warehouse, ProductVariant $variant, $modifier = 0) {
        $em = $this->container->get('doctrine')->getManager();
        $inventory = $this->getWarehouseInventoryByWarehouse($warehouse, $variant);
        $inventory->setQuantity( $inventory->getQuantity() + $modifier );
        $em->persist($inventory);
        $em->flush();
    }
}