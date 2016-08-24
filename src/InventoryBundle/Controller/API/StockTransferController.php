<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\StockTransfer;
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
        $status = $em->getRepository('InventoryBundle:Status')->find($status_id['id']);

        /**
         * Add status to PO
         * Add order NUmber to PO
         */

        $cart = $request->request->get('cart');
        $due_date = new \DateTime($request->request->get('due_date'));
        $message = $request->request->get('message');
        $receiving_warehouse_id = $request->request->get('receiving_warehouse_id');
        $departing_warehouse_id = $request->request->get('departing_warehouse_id');
        $departing_warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($departing_warehouse_id);
        $receiving_warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($receiving_warehouse_id);

        $stock_transfer = new StockTransfer();
        $stock_transfer->setUser($this->getUser());
        $stock_transfer->setWarehouse($warehouse);
        $stock_transfer->setStockDueDate($due_date);
        $stock_transfer->setMessage($message);
        $stock_transfer->setStatus($status);


        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
            $purchase_order_variant = new PurchaseOrderProductVariant();
            $purchase_order_variant->setProductVariant($variant);
            $purchase_order_variant->setPurchaseOrder($stock_transfer);
            $purchase_order_variant->setOrderedQuantity($item['add_quantity']);
            $em->persist($purchase_order_variant);
        }
        $em->persist($stock_transfer);
        $em->flush();

        $stock_transfer->setOrderNumber('PO-'. str_pad($stock_transfer->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($stock_transfer);
        $em->flush();

        return JsonResponse::create($stock_transfer->getId());
    }
}
