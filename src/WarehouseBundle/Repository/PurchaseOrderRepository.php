<?php

namespace WarehouseBundle\Repository;
use WarehouseBundle\Entity\PurchaseOrder;
use WarehouseBundle\Entity\Warehouse;

/**
 * PurchaseOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PurchaseOrderRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCartArray(PurchaseOrder $purchaseOrder)
    {
        $em = $this->getEntityManager();

        $cart = array();
        $total = 0;
        foreach($purchaseOrder->getProductvariants() as $variant) {
            $image_url = '/';
            foreach($variant->getProductVariant()->getProduct()->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            $total += $variant->getOrderedQuantity();

            if($purchaseOrder->getStatus()->getName() === 'Received') {
                $tq = $variant->getTotalQuantityAfter();
                $wq = $variant->getWarehouseQuantityAfter();
            }
            else {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id");
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $total_quantity = $statement->fetch();
                $tq = $total_quantity['total'] + $variant->getOrderedQuantity();

                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->bindValue('warehouse_id', $purchaseOrder->getWarehouse()->getId());
                $statement->execute();
                $warehouse_quantity = $statement->fetch();
                $wq = $warehouse_quantity['total'] + $variant->getOrderedQuantity();
            }

            $cart[] = array(
                'name' => $variant->getProductVariant()->getProduct()->getName().": ".$variant->getProductVariant()->getName(),
                'id' => $variant->getProductVariant()->getId(),
                'purchase_order_product_variant_id' => $variant->getId(),
                'image_url' => $image_url,
                'total_quantity' => $tq,
                'warehouse_quantity' => $wq,
                'ordered_quantity' => $variant->getOrderedQuantity(),
                'received_quantity' => ($purchaseOrder->getStatus()->getName() === 'Received' ? $variant->getReceivedQuantity() : $variant->getOrderedQuantity())
            );
        }
        return array(
            'cart' => $cart,
            'total' => $total
        );
    }

    public function getActiveForWarehouseArray(Warehouse $warehouse) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'purchase_order' as type
	from purchase_order p
		left join warehouses w
			on p.warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id 
	and s.name = 'Active'");
        $statement->bindValue('warehouse_id', $warehouse->getId());
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getAllForWarehouseArray(Warehouse $warehouse) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'purchase_order' as type
	from purchase_order p
		left join warehouses w
			on p.warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id");
        $statement->bindValue('warehouse_id', $warehouse->getId());
        $statement->execute();
        return $statement->fetchAll();
    }

}