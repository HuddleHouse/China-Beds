<?php

namespace InventoryBundle\Repository;
use InventoryBundle\Entity\PurchaseOrder;
use InventoryBundle\Entity\StockTransfer;
use InventoryBundle\Entity\Warehouse;

/**
 * StockTransferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockTransferRepository extends \Doctrine\ORM\EntityRepository
{

    public function getCartArray(StockTransfer $stockTransfer)
    {
        $em = $this->getEntityManager();

        $cart = array();
        $total = 0;
        foreach($stockTransfer->getProductvariants() as $variant) {
            $image_url = '/';
            foreach($variant->getProductVariant()->getProduct()->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
            $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
            $statement->bindValue('warehouse_id', $stockTransfer->getDepartingWarehouse()->getId());
            $statement->execute();
            $departing_warehouse_quantity = $statement->fetch();

            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT COALESCE(sum(quantity),0) as total FROM warehouse_inventory WHERE product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
            $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
            $statement->bindValue('warehouse_id', $stockTransfer->getReceivingWarehouse()->getId());
            $statement->execute();
            $receiving_warehouse_quantity = $statement->fetch();

            $total += $variant->getQuantity();

            $cart[] = array(
                'name' => $variant->getProductVariant()->getProduct()->getName().": ".$variant->getProductVariant()->getName(),
                'id' => $variant->getProductVariant()->getId(),
                'stock_transfer_product_variant_id' => $variant->getId(),
                'image_url' => $image_url,
                'quantity' => $variant->getQuantity(),
                'departing_warehouse_quantity' => $departing_warehouse_quantity['total'] - $variant->getQuantity(),
                'receiving_warehouse_quantity' => $receiving_warehouse_quantity['total'] + $variant->getQuantity()
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
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'stock_transfer' as type
	from stock_transfers p 
		left join warehouses w
			on p.receiving_warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id
	and s.name = 'Active'
	union
select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'stock_transfer' as type
	from stock_transfers p 
		left join warehouses w
			on p.departing_warehouse_id = w.id
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
        $statement = $connection->prepare("select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'stock_transfer' as type
	from stock_transfers p 
		left join warehouses w
			on p.receiving_warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id
	and s.name = 'Active'
	union
select p.*, s.color, s.name as status_name, w.name as warehouse_name, 'stock_transfer' as type
	from stock_transfers p 
		left join warehouses w
			on p.departing_warehouse_id = w.id
		left join status s
			on s.id = p.status_id
	where w.id = :warehouse_id");
        $statement->bindValue('warehouse_id', $warehouse->getId());
        $statement->execute();
        return $statement->fetchAll();
    }

}
