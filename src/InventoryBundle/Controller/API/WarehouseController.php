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
class WarehouseController extends Controller
{

    /**
     * @Route("/api_get_all_warehouse_products", name="api_get_all_warehouse_products")
     */
    public function getAllProductsArray()
    {
        $em = $this->getDoctrine()->getManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $image_url = '';
            foreach($prod->getImages() as $image) {
                $image_url = $image->getWebPath();
                break;
            }

            foreach($prod->getVariants() as $variant)
                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId(),
                    'image_url' => $image_url
                );
        }

        return JsonResponse::create($products);
    }

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

        $purchase_order = new PurchaseOrder();
        $purchase_order->setUser($this->getUser());
        $purchase_order->setWarehouse($warehouse);
        $purchase_order->setStockDueDate($due_date);
        $purchase_order->setMessage($message);
        $purchase_order->setStatus($status);


        foreach($cart as $item) {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['id']);
            $purchase_order_variant = new PurchaseOrderProductVariant();
            $purchase_order_variant->setProductVariant($variant);
            $purchase_order_variant->setPurchaseOrder($purchase_order);
            $purchase_order_variant->setOrderedQuantity($item['add_quantity']);
            $em->persist($purchase_order_variant);
        }
        $em->persist($purchase_order);
        $em->flush();

        $purchase_order->setOrderNumber('PO-'. str_pad($purchase_order->getId(), 5, "0", STR_PAD_LEFT));
        $em->persist($purchase_order);
        $em->flush();

        return JsonResponse::create($purchase_order->getId());
    }


}

