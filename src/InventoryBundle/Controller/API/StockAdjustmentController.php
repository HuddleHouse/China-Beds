<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\StockAdjustment;
use InventoryBundle\Entity\StockAdjustmentProductVariant;
use InventoryBundle\Entity\StockTransfer;
use InventoryBundle\Entity\StockTransferProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class StockAdjustmentController extends Controller
{
    /**
     * @Route("/api_save_stock_adjustment", name="api_save_stock_adjustment")
     */
    public function saveStockAdjustment(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $status_name = $request->request->get('status');
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM status WHERE name = :name");
        $statement->bindValue('name', $status_name);
        $statement->execute();
        $status_id = $statement->fetch();
        $status = $em->getRepository('InventoryBundle:Status')->find($status_id['id']);

        $cart = $request->request->get('cart');
        $date = new \DateTime($request->request->get('date'));
        $message = $request->request->get('message');
        $warehouse_id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($warehouse_id);

        $stock_adjustment_id = $request->request->get('stock_adjustment_id');
        if($stock_adjustment_id != null)
            $stock_adjustment = $em->getRepository('InventoryBundle:StockAdjustment')->find($stock_adjustment_id);
        else
            $stock_adjustment = new StockAdjustment();
        $stock_adjustment->setUser($this->getUser());
        $stock_adjustment->setWarehouse($warehouse);
        $stock_adjustment->setDate($date);
        $stock_adjustment->setMessage($message);
        $stock_adjustment->setStatus($status);

        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
            if(isset($item['stock_adjustment_product_variant_id']))
                $stock_transfer_variant = $em->getRepository('InventoryBundle:StockAdjustmentProductVariant')->find($item['stock_transfer_product_variant_id']);
            else {
                $stock_transfer_variant = new StockAdjustmentProductVariant();
                $stock_transfer_variant->setProductVariant($variant);
                $stock_transfer_variant->setStockAdjustment($stock_adjustment);
            }

            $stock_transfer_variant->setQuantity($item['quantity']);
            $em->persist($stock_transfer_variant);
        }
        $em->persist($stock_adjustment);
        $em->flush();

        $stock_adjustment->setOrderNumber('SA-'. str_pad($stock_adjustment->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($stock_adjustment);
        $em->flush();

        $complete = $request->request->get('complete');

        if($complete != null) {
            foreach($stock_adjustment->getProductVariants() as $variant) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT id FROM warehouse_inventory where warehouse_id = :warehouse_id and product_variant_id = :product_variant_id");
                $statement->bindValue('warehouse_id', $receiving_warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $receiving_warehouse_inventory_id = $statement->fetch();

                if($receiving_warehouse_inventory_id != false)
                    $em->getRepository('InventoryBundle:Warehouse')->updateWarehouseInventoryById($receiving_warehouse_inventory_id['id'], $variant->getQuantity());
                else {
                    $em->getRepository('InventoryBundle:Warehouse')->addWarehouseInventory($receiving_warehouse, $variant->getProductVariant(), $variant->getQuantity());
                }

                $statement = $connection->prepare("SELECT id FROM warehouse_inventory where warehouse_id = :warehouse_id and product_variant_id = :product_variant_id");
                $statement->bindValue('warehouse_id', $warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $departing_warehouse_inventory_id = $statement->fetch();

                if($departing_warehouse_inventory_id != false)
                    $em->getRepository('InventoryBundle:Warehouse')->updateWarehouseInventoryById($departing_warehouse_inventory_id['id'], $variant->getQuantity()*-1);
                else
                    $em->getRepository('InventoryBundle:Warehouse')->addWarehouseInventory($warehouse, $variant->getProductVariant(), $variant->getQuantity()*-1);

            }
        }

        return JsonResponse::create($stock_adjustment->getId());
    }
}
