<?php

namespace WarehouseBundle\Controller\API;

use WarehouseBundle\Entity\StockTransfer;
use WarehouseBundle\Entity\StockTransferProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class StockTransferController extends Controller
{
    /**
     * @Route("/api_save_stock_transfer", name="api_save_stock_transfer")
     */
    public function saveStockTransfer(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $status_name = $request->request->get('status');
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM status WHERE name = :name");
        $statement->bindValue('name', $status_name);
        $statement->execute();
        $status_id = $statement->fetch();
        $status = $em->getRepository('WarehouseBundle:Status')->find($status_id['id']);

        $cart = $request->request->get('cart');
        $cart_total_before = $cart[0]["departing_warehouse_quantity"] + $cart[0]["quantity"];

        $due_date = new \DateTime($request->request->get('transfer_date'));
        $message = $request->request->get('message');
        $receiving_warehouse_id = $request->request->get('receiving_warehouse_id');
        $departing_warehouse_id = $request->request->get('departing_warehouse_id');
        $departing_warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($departing_warehouse_id);
        $receiving_warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($receiving_warehouse_id);

        $stock_transfer_id = $request->request->get('stock_transfer_id');
        if($stock_transfer_id != null)
            $stock_transfer = $em->getRepository('WarehouseBundle:StockTransfer')->find($stock_transfer_id);
        else
            $stock_transfer = new StockTransfer();
        $stock_transfer->setUser($this->getUser());
        $stock_transfer->setDepartingWarehouse($departing_warehouse);
        $stock_transfer->setReceivingWarehouse($receiving_warehouse);
        $stock_transfer->setDate($due_date);
        $stock_transfer->setMessage($message);
        $stock_transfer->setStatus($status);

        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
            if(isset($item['stock_transfer_product_variant_id']))
                $stock_transfer_variant = $em->getRepository('WarehouseBundle:StockTransferProductVariant')->find($item['stock_transfer_product_variant_id']);
            else {
                $stock_transfer_variant = new StockTransferProductVariant();
                $stock_transfer_variant->setProductVariant($variant);
                $stock_transfer_variant->setStockTransfer($stock_transfer);
            }
            $stock_transfer_variant->setDepartingWarehouseQuantityAfter($item['departing_warehouse_quantity']);
            $stock_transfer_variant->setReceivingWarehouseQuantityAfter($item['receiving_warehouse_quantity']);
            $stock_transfer_variant->setQuantity($item['quantity']);
            $em->persist($stock_transfer_variant);
        }
        $em->persist($stock_transfer);
        $em->flush();

        $stock_transfer->setOrderNumber('ST-'. str_pad($stock_transfer->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($stock_transfer);
        $em->flush();

        $complete = $request->request->get('complete');

        if($complete != "false") {
            foreach($stock_transfer->getProductVariants() as $variant) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT id FROM warehouse_inventory where warehouse_id = :warehouse_id and product_variant_id = :product_variant_id");
                $statement->bindValue('warehouse_id', $receiving_warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $receiving_warehouse_inventory_id = $statement->fetch();

                if($receiving_warehouse_inventory_id != false)
                    $em->getRepository('WarehouseBundle:Warehouse')->updateWarehouseInventoryById($receiving_warehouse_inventory_id['id'], $variant->getQuantity());
                else {
                    $em->getRepository('WarehouseBundle:Warehouse')->addWarehouseInventory($receiving_warehouse, $variant->getProductVariant(), $variant->getQuantity());
                }

                $statement = $connection->prepare("SELECT id FROM warehouse_inventory where warehouse_id = :warehouse_id and product_variant_id = :product_variant_id");
                $statement->bindValue('warehouse_id', $departing_warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $departing_warehouse_inventory_id = $statement->fetch();

                if($departing_warehouse_inventory_id != false)
                    $em->getRepository('WarehouseBundle:Warehouse')->updateWarehouseInventoryById($departing_warehouse_inventory_id['id'], $variant->getQuantity()*-1);
                else
                    $em->getRepository('WarehouseBundle:Warehouse')->addWarehouseInventory($departing_warehouse, $variant->getProductVariant(), $variant->getQuantity()*-1);

            }
        }

        return JsonResponse::create($stock_transfer->getId());
    }


    /**
     * @Route("/api_set_stock_transfer_active", name="api_set_stock_transfer_active")
     */
    public function setStockTransferActive(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $stock_adjustment = $em->getRepository('WarehouseBundle:StockTransfer')->find($id);

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM status WHERE name = :name");
        $statement->bindValue('name', 'Active');
        $statement->execute();
        $status_id = $statement->fetch();
        $status = $em->getRepository('WarehouseBundle:Status')->find($status_id['id']);

        $stock_adjustment->setStatus($status);

        $em->persist($stock_adjustment);
        $em->flush();

        return JsonResponse::create($stock_adjustment->getId());
    }

}
