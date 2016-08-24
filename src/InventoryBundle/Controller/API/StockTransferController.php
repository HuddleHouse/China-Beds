<?php

namespace InventoryBundle\Controller\API;

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
        $due_date = new \DateTime($request->request->get('transfer_date'));
        $message = $request->request->get('message');
        $receiving_warehouse_id = $request->request->get('receiving_warehouse_id');
        $departing_warehouse_id = $request->request->get('departing_warehouse_id');
        $departing_warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($departing_warehouse_id);
        $receiving_warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($receiving_warehouse_id);

        $stock_transfer = new StockTransfer();
        $stock_transfer->setUser($this->getUser());
        $stock_transfer->setDepartingWarehouse($departing_warehouse);
        $stock_transfer->setReceivingWarehouse($receiving_warehouse);
        $stock_transfer->setDate($due_date);
        $stock_transfer->setMessage($message);
        $stock_transfer->setStatus($status);


        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
            $stock_transfer_variant = new StockTransferProductVariant();
            $stock_transfer_variant->setProductVariant($variant);
            $stock_transfer_variant->setStockTransfer($stock_transfer);
            $stock_transfer_variant->setQuantity($item['ordered_quantity']);
            $em->persist($stock_transfer_variant);
        }
        $em->persist($stock_transfer);
        $em->flush();

        $stock_transfer->setOrderNumber('ST-'. str_pad($stock_transfer->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($stock_transfer);
        $em->flush();

        return JsonResponse::create($stock_transfer->getId());
    }
}
