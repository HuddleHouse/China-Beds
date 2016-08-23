<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\PurchaseOrder;
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
        $cart = $request->request->get('cart');
        $due_date = new \DateTime($request->request->get('due_date'));
        $message = $request->request->get('message');
        $warehouse_id = $request->request->get('warehouse_id');

        $warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($warehouse_id);
        $purchase_order = new PurchaseOrder();

        foreach($cart as $item) {
            $image_url = '';

        }

        return JsonResponse::create($products);
    }
}

