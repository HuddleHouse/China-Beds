<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\PurchaseOrder;
use InventoryBundle\Entity\PurchaseOrderProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class PurchaseOrderController extends Controller
{

    /**
     * @Route("/api_save_purchase_order", name="api_save_purchase_order")
     */
    public function savePurchaseOrder(Request $request)
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
        $warehouse_id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($warehouse_id);

        $purchase_order_id = $request->request->get('purchase_order_id');
        if($purchase_order_id != null)
            $purchase_order = $em->getRepository('InventoryBundle:PurchaseOrder')->find($purchase_order_id);
        else
            $purchase_order = new PurchaseOrder();
        $purchase_order->setUser($this->getUser());
        $purchase_order->setWarehouse($warehouse);
        $purchase_order->setStockDueDate($due_date);
        $purchase_order->setMessage($message);
        $purchase_order->setStatus($status);


        foreach($cart as $item) {
            if(isset($item['purchase_order_product_variant_id']))
                $purchase_order_variant = $em->getRepository('InventoryBundle:PurchaseOrderProductVariant')->find($item['purchase_order_product_variant_id']);
            else
            {
                $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
                $purchase_order_variant = new PurchaseOrderProductVariant();
                $purchase_order_variant->setProductVariant($variant);
                $purchase_order_variant->setPurchaseOrder($purchase_order);
            }

            $purchase_order_variant->setTotalQuantityAfter($item['total_quantity']);
            $purchase_order_variant->setWarehouseQuantityAfter($item['warehouse_quantity']);
            $purchase_order_variant->setOrderedQuantity($item['ordered_quantity']);
            $em->persist($purchase_order_variant);
        }
        $em->persist($purchase_order);
        $em->flush();

        $purchase_order->setOrderNumber('PO-'. str_pad($purchase_order->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($purchase_order);
        $em->flush();

        return JsonResponse::create($purchase_order->getId());
    }

    
    /**
     * @Route("/api_set_purchase_order_active", name="api_set_purchase_order_active")
     */
    public function setPurchaseOrderActive(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $purchase_order = $em->getRepository('InventoryBundle:PurchaseOrder')->find($id);

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM status WHERE name = :name");
        $statement->bindValue('name', 'Active');
        $statement->execute();
        $status_id = $statement->fetch();
        $status = $em->getRepository('InventoryBundle:Status')->find($status_id['id']);

        $purchase_order->setStatus($status);

        $em->persist($purchase_order);
        $em->flush();

        return JsonResponse::create($purchase_order->getId());
    }


    /**
     * @Route("/api_purchase_order_receive_all", name="api_purchase_order_receive_all")
     */
    public function purchaseOrderReceiveAllActive(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $request->request->get('cart');
        $due_date = new \DateTime($request->request->get('due_date'));
        $message = $request->request->get('message');
        $po_id = $request->request->get('purchase_order_id');
        $po = $em->getRepository('InventoryBundle:PurchaseOrder')->find($po_id);
        $warehouse = $po->getWarehouse();

        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:PurchaseOrderProductVariant')->find($item['purchase_order_product_variant_id']);
            $variant->setReceivedQuantity($item['received_quantity']);
            $variant->setTotalQuantityAfter($item['total_quantity']);
            $variant->setWarehouseQuantityAfter($item['warehouse_quantity']);
            $variant->setOrderedQuantity($item['ordered_quantity']);
            $em->persist($variant);

            $connection = $em->getConnection();
            $statement = $connection->prepare("select i.id as id
	from warehouse_inventory i
	where i.warehouse_id = :warehouse_id
	and i.product_variant_id = :product_variant_id");
            $statement->bindValue('warehouse_id', $warehouse->getId());
            $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());
            $statement->execute();
            $warehouse_inventory_id = $statement->fetch();

            if($warehouse_inventory_id == false) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("insert into warehouse_inventory (quantity, warehouse_id, product_variant_id) values (:quantity, :warehouse_id, :product_variant_id)");
                $statement->bindValue('quantity', $item['received_quantity']);
                $statement->bindValue('warehouse_id', $warehouse->getId());
                $statement->bindValue('product_variant_id', $variant->getProductVariant()->getId());

                try {
                    $statement->execute();
                }
                catch(\Exception $e) {
                    return JsonResponse::create(false);
                }
            }
            else {
                $warehouse_inventory = $em->getRepository('InventoryBundle:WarehouseInventory')->find($warehouse_inventory_id['id']);
                $warehouse_inventory->setQuantity($warehouse_inventory->getQuantity() + $item['received_quantity']);
                $em->persist($warehouse_inventory);
            }
        }

        $statement = $connection->prepare("SELECT id FROM status WHERE name = :name");
        $statement->bindValue('name', 'Received');
        $statement->execute();
        $status_id = $statement->fetch();
        $status = $em->getRepository('InventoryBundle:Status')->find($status_id['id']);

        $po->setStatus($status);
        $em->persist($po);
        try {
            $em->flush();
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }


        return JsonResponse::create($po->getId());
    }

}

