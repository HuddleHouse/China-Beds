<?php

namespace InventoryBundle\Repository;
use InventoryBundle\Entity\PurchaseOrder;
use InventoryBundle\Entity\StockAdjustment;
use InventoryBundle\Entity\StockTransfer;
use InventoryBundle\Entity\Warehouse;

/**
 * StockTransferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockAdjustmentRepository extends \Doctrine\ORM\EntityRepository
{

    public function getCartArray(StockAdjustment $stockAdjustment)
    {
        $em = $this->getEntityManager();

        $cart = array();
        $total = 0;
        foreach($stockAdjustment->getProductvariants() as $variant) {
            $image_url = '/';
            foreach($variant->getProductVariant()->getProduct()->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
            $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
            $statement->bindValue('warehouse_id', $stockAdjustment->getWarehouse()->getId());
            $statement->execute();
            $warehouse_quantity = $statement->fetch();

            $total += $variant->getQuantity();

            $cart[] = array(
                'name' => $variant->getProductVariant()->getProduct()->getName().": ".$variant->getProductVariant()->getName(),
                'id' => $variant->getProductVariant()->getId(),
                'stock_adjustment_product_variant_id' => $variant->getId(),
                'image_url' => $image_url,
                'quantity' => $variant->getQuantity(),
                'warehouse_quantity' => $warehouse_quantity['total'] - $variant->getQuantity()
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
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name
	from warehouses w
		left join purchase_order p
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
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name
	from warehouses w
		left join purchase_order p
			on p.warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id");
        $statement->bindValue('warehouse_id', $warehouse->getId());
        $statement->execute();
        return $statement->fetchAll();
    }

}
