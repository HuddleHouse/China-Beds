<?php

namespace WarehouseBundle\Repository;
use Doctrine\Common\Collections\ArrayCollection;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\ProductVariant;
use OrderBundle\Entity\Orders;
use WarehouseBundle\Entity\Warehouse;
use WarehouseBundle\Entity\WarehouseInventory;

/**
 * WarehouseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WarehouseRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Returns the number of total items in the warehouse
     *
     * @param Warehouse $warehouse
     * @return int
     */
    public function getWarehouseInventory(Warehouse $warehouse)
    {
        if(count($warehouse->getInventory()) == 0)
            return 0;

        $quantity = 0;
        foreach($warehouse->getInventory() as $item)
            $quantity += $item->getQuantity();

        return $quantity;
    }

    public function getWarehouseInventoryOnPurchaseOrder(Warehouse $warehouse)
    {
        $quantity = 0;
        if(count($warehouse->getPurchaseOrders()) == 0)
            return 0;

        foreach($warehouse->getPurchaseOrders() as $item) {
            if($item->getStatus()->getName() == 'Active')
                foreach($item->getProductvariants() as $variant)
                    $quantity += $variant->getOrderedQuantity();
        }

        return $quantity;
    }

//    public function name(product_variantd) {
//        loop through warehouse
//        get prod var
//    }

    public function getWarehouseInventoryForItemOnPurchaseOrder(Warehouse $warehouse, ProductVariant $productVariant)
    {
        $quantity = 0;
        if(count($warehouse->getPurchaseOrders()) == 0)
            return 0;

        foreach($warehouse->getPurchaseOrders() as $item)
            if($item->getStatus()->getName() == 'Active')
                foreach($item->getProductvariants() as $variant)
                    if($variant->getProductVariant()->getId() == $productVariant->getId())
                        $quantity += $variant->getOrderedQuantity();


        return $quantity;
    }

    public function findByChannels($channels = []) {
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT w FROM WarehouseBundle:Warehouse w LEFT JOIN w.channel c WHERE c IN (:channels) and w.active = TRUE'
            )
            ->setParameter('channels', $channels)
            ->getResult();
        return $result;
    }

    public function getAllWarehousesArray(Channel $channel = null) {
        $warehouses = $this->findByChannels($channel);
        $data = array();

        foreach($warehouses as $warehouse)
            if ( $warehouse->isActive() ) {
                $data[] = array(
                    'id' => $warehouse->getId(),
                    'name' => $warehouse->getName(),
                    'list_id' => $warehouse->getListId(),
                    'quantity' => $this->getWarehouseInventory($warehouse),
                    'po_quantity' => $this->getWarehouseInventoryOnPurchaseOrder($warehouse),
                    'active' => $warehouse->isActive()
                );
            }

        return $data;
    }

    public function getAllWarehousesInChannelArray($channelName) {
        //$warehouses = $this->findAll();
        $warehouses = $this->findBy(array('active' => true));
        $data = array();

        foreach($warehouses as $warehouse)
            if(in_array($channelName, $warehouse->getChannelNamesArray()))
                $data[] = array(
                    'id' => $warehouse->getId(),
                    'name' => $warehouse->getName(),
                    'list_id' => $warehouse->getListId(),
                    'quantity' => $this->getWarehouseInventory($warehouse),
                    'po_quantity' => $this->getWarehouseInventoryOnPurchaseOrder($warehouse)
                );

        return $data;
    }

    /**
     *
     * @param Warehouse $warehouse
     * @return array|bool
     */
    public function getWarehouseInventoryArray(Warehouse $warehouse) {

        foreach($warehouse->getInventory() as $item) {
            $inventory_data[] = array(
                'id' => $item->getId(),
                'name' => $item->getProductVariant()->getProduct()->getName().": ".$item->getProductVariant()->getName(),
                'quantity' => $item->getQuantity(),
                'po_quantity' => $this->getWarehouseInventoryForItemOnPurchaseOrder($warehouse, $item->getProductVariant())
            );
        }

        if(!isset($inventory_data))
            return true;

        return $inventory_data;
    }

    /**
     *
     * @param Warehouse $warehouse
     * @return array|bool
     */
    public function getWarehousePopInventoryArray(Warehouse $warehouse) {
        $i = 1;
        foreach($warehouse->getPopInventory() as $item) {
            $inventory_data[] = array(
                'id' => $item->getId(),
                'name' => $item->getPopItem()->getName(),
                'quantity' => $item->getQuantity(),
            );
        }

        if(!isset($inventory_data))
            return true;

        return $inventory_data;
    }

    /**
     * @param WarehouseInventory $id
     * @param Warehouse $warehouse
     * @param $quantity
     */
    public function updateWarehouseInventoryById($id, $quantity) {
        $em = $this->getEntityManager();

        $warehouseInventory = $em->getRepository('WarehouseBundle:WarehouseInventory')->find($id);
        if($warehouseInventory) {
            $tmp = $warehouseInventory->getQuantity() + (int)$quantity;
            $warehouseInventory->setQuantity($tmp);
            $em->persist($warehouseInventory);
            $em->flush();
        }

        return $warehouseInventory;
    }

    /**
     * @param Warehouse $warehouse
     * @param ProductVariant $productVariant
     * @param $quantity
     */
    public function addWarehouseInventory(Warehouse $warehouse, ProductVariant $productVariant, $quantity) {
        $em = $this->getEntityManager();
        $warehouseInventory = new WarehouseInventory();
        $warehouseInventory->setWarehouse($warehouse);
        $warehouseInventory->setProductVariant($productVariant);
        $warehouseInventory->setQuantity((int)$quantity);
        $em->persist($warehouseInventory);
        $em->flush();

        return $warehouseInventory;
    }


    public function getInventoryForProduct(ProductVariant $productVariant, Warehouse $warehouse)
    {
        $em = $this->getEntityManager();
        $warehouse_id = $warehouse->getId();
        $product_variant_id = $productVariant->getId();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT quantity from warehouse_inventory where product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
        $statement->bindValue('product_variant_id', $product_variant_id);
        $statement->bindValue('warehouse_id', $warehouse_id);
        $statement->execute();
        $quantity = $statement->fetch();

        if($quantity == false)
            return 0;
        else
            return $quantity['quantity'];
    }

    public function getWarehousesForOrder(Orders $order) {
        $warehouses = new ArrayCollection();

        $em = $this->getEntityManager();
        $orders = $em
            ->createQuery("SELECT o, v, i FROM OrderBundle\Entity\Orders o JOIN o.product_variants v JOIN v.warehouse_info i WHERE o.id = :id")
            ->setParameter('id', $order)
            ->getResult();

        foreach($orders as $order) {
            foreach($order->getProductVariants() as $variant) {
                foreach($variant->getWarehouseInfo() as $info) {
                    $warehouses->add($info->getWarehouse());
                }
            }
        }

        return $warehouses;
    }
}
