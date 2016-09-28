<?php

namespace OrderBundle\Controller\API;

use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersProductVariant;
use OrderBundle\Entity\OrdersWarehouseInfo;
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

        $products = $request->request->get('products');
        $pop = $request->request->get('pop');
        $cart = $request->request->get('cart');
        $total = $request->request->get('total');
        $info = $request->request->get('form_info');
        $ship_to_user_id = $request->request->get('ship_to_user_id');
        //array indexed at prod variant id that tell you the ordered quantity
        $product_variant_order_quan = $request->request->get('product_variant_order_quan');

        $order = new Orders($info);
        $em->persist($order);

        $status = $em->getRepository('WarehouseBundle:Status')->getStatusByName('Draft');
        $order->setStatus($status);
        $order->setSubtotal($total);
        $order->setChannel($channel);
        $order->setSubmittedByUser($this->getUser());
        if($this->getUser()->getId() == $ship_to_user_id)
            $order->setSubmittedByUser($this->getUser());
        else
            $order->setSubmittedByUser($em->getRepository('AppBundle:User')->find($ship_to_user_id));

        $state = $em->getRepository('AppBundle:State')->find($info['state']);
        $order->setState($state);

        foreach($products as $product) {
            if(isset($product['variants'])) {
                foreach($product['variants'] as $variant) {
                    $quantity = $product_variant_order_quan[$variant['variant_id']];
                    if($quantity > 0) {
                        $product_variant = $em->getRepository('InventoryBundle:ProductVariant')->find($variant['variant_id']);
                        $orders_product_variant = new OrdersProductVariant();
                        $orders_product_variant->setOrder($order);
                        $orders_product_variant->setPrice($variant['cost']);
                        $orders_product_variant->setQuantity($quantity);
                        $orders_product_variant->setProductVariant($product_variant);
                        $em->persist($orders_product_variant);
                        $em->flush();

                        //we passed in the complete warehouse quantities at start so we know where to go ahead and pull the inventory from.
                        foreach($variant['warehouse_data'] as $warehouse_data) {
                            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_data['warehouse_id']);
                            if($quantity <= $warehouse_data['quantity']) {
                                $orders_warehouse_info = new OrdersWarehouseInfo($quantity, $orders_product_variant, $warehouse);
                                $em->persist($orders_warehouse_info);
                                $orders_product_variant->addWarehouseInfo($orders_warehouse_info);
                                break;
                            }
                            else if($quantity > $warehouse_data['quantity']) {
                                $orders_warehouse_info = new OrdersWarehouseInfo($warehouse_data['quantity'], $orders_product_variant, $warehouse);
                                $quantity -= $warehouse_data['quantity'];
                            }
                            $em->persist($orders_warehouse_info);
                            $orders_product_variant->addWarehouseInfo($orders_warehouse_info);
                        }
                        $em->persist($orders_product_variant);

                        $order->addProductVariants($orders_product_variant);
                    }
                }
            }
        }

        $em->persist($order);
        $em->flush();

//        $em->getRepository('OrderBundle:Orders')->setWarehouseDataForOrder($order);

        return JsonResponse::create($order->getId());
    }

    /**
     * @Route("/api_update_products_for_channel", name="api_update_products_for_channel")
     */
    public function updateProductsForChannel(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $warehouse_id = $request->request->get('warehouse_id');
        if($warehouse_id != null)
            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

        $user_id = $request->request->get('user_id');
        $channel_id = $request->request->get('channel_id');
        $user = $em->getRepository('AppBundle:User')->find($user_id);
        $channel = $em->getRepository('InventoryBundle:Channel')->find($channel_id);

        if($warehouse_id != null)
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user, $warehouse);
        else
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user);


        return JsonResponse::create($product_data);
    }


    /**
     * @Route("/api_get_user_info_for_order_form", name="api_get_user_info_for_order_form")
     */
    public function getUserInforForOrderForm(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $request->request->get('user_id');
        $user = $em->getRepository('AppBundle:User')->find($user_id);

        $data = array(
            'ship_name' => $user->getFullName(),
            'address' => $user->getAddress1(),
            'address2' => $user->getAddress2(),
            'city' => $user->getCity(),
            'state' => (string)$user->getState()->getId(),
            'zip' => $user->getZip(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail()
            );

        return JsonResponse::create($data);
    }
}

