<?php

namespace OrderBundle\Repository;
use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersWarehouseInfo;

/**
 * OrdersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrdersRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * THIS IS OLD AND DOES NOT GET USED ANYMORE.
     */
    public function setWarehouseDataForOrder(Orders $order) {
        $em = $this->getEntityManager();

        foreach($order->getProductVariants() as $productVariant) {
            $warehouse1_inventory = $em->getRepository('WarehouseBundle:Warehouse')->getInventoryForProduct($productVariant->getProductVariant(), $order->getUser()->getWarehouse1());
            $warehouse2_inventory = $em->getRepository('WarehouseBundle:Warehouse')->getInventoryForProduct($productVariant->getProductVariant(), $order->getUser()->getWarehouse2());
            $warehouse3_inventory = $em->getRepository('WarehouseBundle:Warehouse')->getInventoryForProduct($productVariant->getProductVariant(), $order->getUser()->getWarehouse3());
            $ordered_quantity = $productVariant->getQuantity();
            $warehouse_info = $productVariant->getWarehouseInfo();
            $warehouse_info->clear();
            $productVariant = $em->merge($productVariant);
            $em->flush();

            if($ordered_quantity <= $warehouse1_inventory) {
                $order_warehouse_info = new OrdersWarehouseInfo($ordered_quantity, $productVariant, $order->getUser()->getWarehouse1());
                $em->persist($order_warehouse_info);
                $productVariant->addWarehouseInfo($order_warehouse_info);
            }
            else if($ordered_quantity <= ($warehouse1_inventory + $warehouse2_inventory) || $ordered_quantity <= ($warehouse1_inventory + $warehouse3_inventory)) {
                if($ordered_quantity <= ($warehouse1_inventory + $warehouse2_inventory)) {
                    $order_warehouse_info = new OrdersWarehouseInfo($warehouse1_inventory, $productVariant, $order->getUser()->getWarehouse1());
                    $em->persist($order_warehouse_info);
                    $productVariant->addWarehouseInfo($order_warehouse_info);

                    $ordered_quantity -= $warehouse1_inventory;
                    $order_warehouse_info = new OrdersWarehouseInfo($ordered_quantity, $productVariant, $order->getUser()->getWarehouse2());
                    $em->persist($order_warehouse_info);
                    $productVariant->addWarehouseInfo($order_warehouse_info);
                }
                else {
                    $order_warehouse_info = new OrdersWarehouseInfo($warehouse1_inventory, $productVariant, $order->getUser()->getWarehouse1());
                    $em->persist($order_warehouse_info);
                    $productVariant->addWarehouseInfo($order_warehouse_info);

                    $ordered_quantity -= $warehouse1_inventory;
                    $order_warehouse_info = new OrdersWarehouseInfo($ordered_quantity, $productVariant, $order->getUser()->getWarehouse3());
                    $em->persist($order_warehouse_info);
                    $productVariant->addWarehouseInfo($order_warehouse_info);
                }
            }
            else {
                if($warehouse1_inventory != 0) {
                    $order_warehouse_info = new OrdersWarehouseInfo($warehouse1_inventory, $productVariant, $order->getUser()->getWarehouse1());
                    $em->persist($order_warehouse_info);
                    $ordered_quantity -= $warehouse1_inventory;
                    $productVariant->addWarehouseInfo($order_warehouse_info);
                }
                if($warehouse2_inventory != 0) {
                    $order_warehouse_info = new OrdersWarehouseInfo($warehouse2_inventory, $productVariant, $order->getUser()->getWarehouse2());
                    $em->persist($order_warehouse_info);
                    $ordered_quantity -= $warehouse2_inventory;
                    $productVariant->addWarehouseInfo($order_warehouse_info);
                }
                if($warehouse3_inventory != 0) {
                    $order_warehouse_info = new OrdersWarehouseInfo($ordered_quantity, $productVariant, $order->getUser()->getWarehouse3());
                    $em->persist($order_warehouse_info);
                    $productVariant->addWarehouseInfo($order_warehouse_info);
                }
            }
        }
        $em->persist($productVariant);
        $em->flush();
    }

    public function getProductsByWarehouseArray(Orders $order) {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select i.*, v.price,((v.price/100)*i.quantity) as subtotal , concat(p.name, ': ', pv.name) as product_name, w.name as warehouse_name, pv.sku
	from orders_warehouse_info i
		left join orders_product_variant v
			on v.id = i.orders_product_variant_id
		left join warehouses w
			on w.id = i.warehouse_id
		left join product_variant pv
			on pv.id = v.product_variant_id
		left join product p
			on p.id = pv.product_id
		left join orders o
			on o.id = v.order_id
	where o.id = :order_id
	order by i.warehouse_id");
        $statement->bindValue('order_id', $order->getId());
        $statement->execute();
        $products = $statement->fetchAll();

        $product_data = array();
        $id = 0;
        foreach($products as $product) {
            if($id == 0)
                $id = $product['warehouse_id'];

            if($id != $product['warehouse_id'])
                $id = $product['warehouse_id'];
            $product_data[$id][] = $product;
        }

        return $product_data;
    }
}
