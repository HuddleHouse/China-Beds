<?php

namespace OrderBundle\Controller\API;

use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersProductVariant;
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
class OrderProductsController extends Controller
{

    /**
     * @Route("/api_save_products_order_form", name="api_save_products_order_form")
     */
    public function saveProductsOrderForm(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel_id = $request->request->get('channel_id');
        $channel = $em->getRepository('InventoryBundle:Channel')->find($channel_id);

        $cart = $request->request->get('cart');
        $total = $request->request->get('total');
        $info = $request->request->get('form_info');
        $products = array();

        $order = new Orders($info);

        $status = $em->getRepository('WarehouseBundle:Status')->getStatusByName('Draft');
        $order->setStatus($status);
        $order->setSubtotal($total);
        $order->setChannel($channel);
        $order->setUser($this->getUser());

        foreach($cart as $item) {
            if($item != '') {
                $product_variant = $em->getRepository('InventoryBundle:ProductVariant')->find($item['variant_id']);
                $orders_product_variant = new OrdersProductVariant();
                $orders_product_variant->setOrder($order);
                $orders_product_variant->setPrice($item['cost']);
                $orders_product_variant->setQuantity($item['quantity']);
                $orders_product_variant->setProductVariant($product_variant);
                $em->persist($orders_product_variant);
                $order->addProductVariants($orders_product_variant);
            }
        }

        $em->persist($order);
        $em->flush();

        $em->getRepository('OrderBundle:Orders')->setWarehouseDataForOrder($order);

        return JsonResponse::create($order->getId());
    }

}

