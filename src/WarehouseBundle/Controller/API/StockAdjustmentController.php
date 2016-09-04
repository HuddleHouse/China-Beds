<?php

namespace WarehouseBundle\Controller\API;

use WarehouseBundle\Entity\StockAdjustment;
use WarehouseBundle\Entity\StockAdjustmentProductVariant;
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
        $status = $em->getRepository('WarehouseBundle:Status')->find($status_id['id']);

        $cart = $request->request->get('cart');
        $date = new \DateTime($request->request->get('date'));
        $message = $request->request->get('message');
        $warehouse_id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

        $stock_adjustment_id = $request->request->get('stock_adjustment_id');
        if($stock_adjustment_id != null)
            $stock_adjustment = $em->getRepository('WarehouseBundle:StockAdjustment')->find($stock_adjustment_id);
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
                $stock_transfer_variant = $em->getRepository('WarehouseBundle:StockAdjustmentProductVariant')->find($item['stock_adjustment_product_variant_id']);
            else {
                $stock_transfer_variant = new StockAdjustmentProductVariant();
                $stock_transfer_variant->setProductVariant($variant);
                $stock_transfer_variant->setStockAdjustment($stock_adjustment);
            }
            $stock_transfer_variant->setTotalQuantityAfter($item['total_quantity']);
            $stock_transfer_variant->setWarehouseQuantityAfter($item['warehouse_quantity']);
            $stock_transfer_variant->setQuantity($item['quantity']);
            $em->persist($stock_transfer_variant);
        }
        $em->persist($stock_adjustment);
        $em->flush();

        $stock_adjustment->setOrderNumber('SA-'. str_pad($stock_adjustment->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($stock_adjustment);
        $em->flush();

        $complete = $request->request->get('complete');

        if($complete != "false") {
            foreach($stock_adjustment->getProductVariants() as $variant) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("SELECT id FROM warehouse_inventory where warehouse_id = :warehouse_id and product_variant_id = :product_variant_id");
                $statement->bindValue('warehouse_id', $warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
                $statement->execute();
                $warehouse_inventory_id = $statement->fetch();

                if($warehouse_inventory_id != false)
                    $em->getRepository('WarehouseBundle:Warehouse')->updateWarehouseInventoryById($warehouse_inventory_id['id'], $variant->getQuantity());
                else {
                    $em->getRepository('WarehouseBundle:Warehouse')->addWarehouseInventory($warehouse, $variant->getProductVariant(), $variant->getQuantity());
                }
            }
        }

        return JsonResponse::create($stock_adjustment->getId());
    }


    /**
     * @Route("/api_set_stock_adjustment_active", name="api_set_stock_adjustment_active")
     */
    public function setStockAdjustmentActive(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $stock_adjustment = $em->getRepository('WarehouseBundle:StockAdjustment')->find($id);

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
