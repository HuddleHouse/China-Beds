<?php

namespace OrderBundle\Controller\API;

use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersPopItem;
use OrderBundle\Entity\OrdersProductVariant;
use OrderBundle\Entity\OrdersWarehouseInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use RocketShipIt;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class OrderProductsController extends Controller
{

    /**
     * @Route("/api_save_products_order_form", name="api_save_products_order_form")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
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
        $order_id = $request->request->get('order_id');
        $info = $request->request->get('form_info');
        $ship_to_user_id = $request->request->get('ship_to_user_id');
        //array indexed at prod variant id that tell you the ordered quantity
        $product_variant_order_quan = $request->request->get('product_variant_order_quan');
        $pop_order_quan = $request->request->get('pop_order_quan');

        if($order_id == 0)
            $order = new Orders($info);
        else {
            $order = $em->getRepository('OrderBundle:Orders')->find($order_id);
            foreach($order->getProductVariants() as $productVariant) {
                foreach($productVariant->getWarehouseInfo() as $item)
                    $em->remove($item);
                $em->remove($productVariant);
            }
            foreach($order->getPopItems() as $productVariant)
                $em->remove($productVariant);

            $order->setData($info);
        }
        $em->persist($order);
        $em->flush();
        $order->setOrderId('O-'. str_pad($order->getId(), 5, "0", STR_PAD_LEFT));

        $status = $em->getRepository('WarehouseBundle:Status')->getStatusByName('Draft');
        $order->setStatus($status);
        $order->setChannel($channel);
        $order->setSubmittedByUser($this->getUser());
        if($this->getUser()->getId() == $ship_to_user_id)
            $order->setSubmittedForUser($this->getUser());
        else
            $order->setSubmittedForUser($em->getRepository('AppBundle:User')->find($ship_to_user_id));

        $state = $em->getRepository('AppBundle:State')->find($info['state']);
        $order->setState($state);

        if($products != null) {
            foreach($products as $product) {
                if(isset($product['variants'])) {
                    foreach($product['variants'] as $variant) {
                        if(isset($product_variant_order_quan[$variant['variant_id']]))
                            $quantity = $product_variant_order_quan[$variant['variant_id']];
                        else
                            $quantity = 0;
                        if($quantity > 0) {
                            $pop_item = $em->getRepository('InventoryBundle:ProductVariant')->find($variant['variant_id']);
                            $orders_product_variant = new OrdersProductVariant();
                            $orders_product_variant->setOrder($order);
                            $orders_product_variant->setPrice($variant['cost']);
                            $orders_product_variant->setQuantity($quantity);
                            $orders_product_variant->setProductVariant($pop_item);
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
        }


        foreach($pop as $popitem) {
            $quantity = 0;
            if(isset($pop_order_quan[$popitem['id']]))
                $quantity = $pop_order_quan[$popitem['id']];
            if($quantity > 0) {
                $pop_item = $em->getRepository('InventoryBundle:PopItem')->find($popitem['id']);
                $orders_pop_item = new OrdersPopItem();
                $orders_pop_item->setOrder($order);
                $orders_pop_item->setPrice($popitem['cost']);
                $orders_pop_item->setQuantity($quantity);
                $orders_pop_item->setPopItem($pop_item);
                $em->persist($orders_pop_item);
                $order->getPopItems()->add($orders_pop_item);
            }
        }
        $shipping = $this->calculateShipping($order);
        $order->setShipping($shipping['rate']);
        $order->setShipCode($shipping['service_code']);
        $order->setShipDescription($shipping['desc']);

        $em->persist($order);
        $em->flush();

//        $em->getRepository('OrderBundle:Orders')->setWarehouseDataForOrder($order);

        return JsonResponse::create($order->getId());
    }

    /**
     * @param Orders $order
     * @return mixed
     *
     * calculates shipping
     */
    private function calculateShipping(Orders $order) {
        $rate = new \RocketShipIt\Rate('fedex');
        $rate->setParameter('toCode', $order->getShipZip());
        $rate->setParameter('residentialAddressIndicator','1');
        $rate->setParameter('service', 'GROUND_HOME_DELIVERY');

        foreach($order->getProductVariants() as $productVariant) {
            $dimensions = explode('x', $productVariant->getProductVariant()->getFedexDimensions());
            $package = new \RocketShipIt\Package('fedex');
            $package->setParameter('length', "$dimensions[0]");
            $package->setParameter('width', "$dimensions[1]");
            $package->setParameter('height', "$dimensions[2]");
            $package->setParameter('weight', $productVariant->getProductVariant()->getWeight());
            $rate->addPackageToShipment($package);
        }



        $response = $rate->getSimpleRates();
        $data = array_pop($response);

        //if $data['rate'] isn't there then they are only ordering pop items, which the shipping for them is on the entity.
        if(!isset($data['rate'])) {
            $data = array();
            $data['rate'] = 0;
            $data['service_code'] = 'FEDEX_GROUND';
            $data['desc'] = 'FedEx Ground';
        }
        $pop_total = 0;
        //Sum up pop items shipping and add it to the shipping amount
        foreach($order->getPopItems() as $popItem)
            $pop_total += $popItem->getPopItem()->getShippingPer();

        $data['rate'] += $pop_total;

        return $data;
    }


    /**
     * @Route("/api_update_products_for_channel", name="api_update_products_for_channel")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function updateProductsForChannel(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $warehouse_id = $request->request->get('warehouse_id');
        if($warehouse_id != null && $warehouse_id != 0)
            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

        $user_id = $request->request->get('user_id');
        $channel_id = $request->request->get('channel_id');
        $user = $em->getRepository('AppBundle:User')->find($user_id);
        $channel = $em->getRepository('InventoryBundle:Channel')->find($channel_id);

        if($warehouse_id != null && $warehouse_id != 0)
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user, $warehouse, null, null, 1);
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

    /**
     * @Route("/api_pay_for_order", name="api_pay_for_order")
     *
     */
    public function payForOrder(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $order_id = $request->request->get('order_id');
        $order = $em->getRepository('OrderBundle:Orders')->find($order_id);


        $payment_type = $request->request->get('payment_type');
        if($payment_type == 'ledger') {
            $ledger_service = $this->get('order.ledger');
            $ledger_service->newEntry($order->getTotal()*-1, $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "Paid for order #".$order->getOrderNumber(), 'Order', $order->getId());
        }
        else if($payment_type == 'cc') {
            $cc = $request->request->get('cc');
            // Charge CC here
        }



        $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));

        $order->setStatus($status);
        $order->setPaymentType($payment_type);
        $order->setAmountPaid($order->getTotal());
        $em->persist($order);
        $em->flush();
        return JsonResponse::create(true);
    }
}

