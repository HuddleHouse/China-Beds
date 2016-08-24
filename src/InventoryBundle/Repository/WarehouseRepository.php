<?php

namespace InventoryBundle\Repository;
use InventoryBundle\Entity\Warehouse;

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

    public function getAllWarehousesArray() {
        $warehouses = $this->findAll();

        foreach($warehouses as $warehouse) {
            $data[] = array(
                'id' => $warehouse->getId(),
                'name' => $warehouse->getName(),
                'list_id' => $warehouse->getListId(),
                'quantity' => $this->getWarehouseInventory($warehouse),
                'po_quantity' => $this->getWarehouseInventoryOnPurchaseOrder($warehouse)
            );
        }
        return $data;
    }

    /**
     * THIS NEEDS TO BE FIXED
     *
     * @param Warehouse $warehouse
     * @return array|bool
     */
    public function getWarehouseInventoryArray(Warehouse $warehouse) {

        foreach($warehouse->getInventory() as $item)
            $inventory_data[] = array(
                'id' => $item->getId(),
                'name' => $item->getProductVariant()->getProduct()->getName().": ".$item->getProductVariant()->getName(),
                'quantity' => $this->getWarehouseInventory($warehouse),
                'po_quantity' => $this->getWarehouseInventoryOnPurchaseOrder($warehouse)
            );

        if(!isset($inventory_data))
            return true;

        return $inventory_data;
    }
}