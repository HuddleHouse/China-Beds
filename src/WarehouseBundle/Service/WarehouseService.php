<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 11/15/16
 * Time: 1:13 AM
 */

namespace WarehouseBundle\Service;


use AppBundle\Services\BaseService;
use InventoryBundle\Entity\ProductVariant;
use OrderBundle\Entity\Orders;
use WarehouseBundle\Entity\Warehouse;

class WarehouseService extends BaseService
{
    public function modifyInventoryLevelForOrder(Orders $order) {
        $em = $this->container->get('doctrine')->getManager();
        foreach($order->getProductVariants() as $variant) {
            foreach($variant->getWarehouseInfo() as $warehouse_info) {
                $inventory = $this->getWarehouseInventoryByWarehouse($warehouse_info->getWarehouse(), $variant->getProductVariant());
                $inventory->setQuantity( $inventory->getQuantity() - $warehouse_info->getQuantity() );
                $em->persist($inventory);
            }
        }
        $em->flush();
    }

    private function getWarehouseInventoryByWarehouse(Warehouse $warehouse, ProductVariant $productVariant) {
        return $this->container->get('doctrine')->getRepository('WarehouseBundle:WarehouseInventory')->findOneBy(['warehouse' => $warehouse, 'product_variant' => $productVariant]);
    }
}