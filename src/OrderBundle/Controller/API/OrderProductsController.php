<?php

namespace OrderBundle\Controller\API;

use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersPopItem;
use OrderBundle\Entity\OrdersProductVariant;
use OrderBundle\Entity\OrdersShippingLabel;
use OrderBundle\Entity\OrdersWarehouseInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $rate->setParameter('toCode', '59759');
        $rate->setParameter('shipCode', '37919');
//        $shipment->setParameter('toCode', '59759');
        $rate->setParameter('residentialAddressIndicator','1');
        $rate->setParameter('service', 'GROUND_HOME_DELIVERY');

        foreach($order->getProductVariants() as $productVariant) {
            for ($x = 0; $x <= $productVariant->getQuantity(); $x++) {
                $dimensions = explode('x', $productVariant->getProductVariant()->getFedexDimensions());
                $package = new \RocketShipIt\Package('fedex');


                $package->setParameter('length', "$dimensions[0]");
                $package->setParameter('width', "$dimensions[1]");
                $package->setParameter('height', "$dimensions[2]");
                $package->setParameter('weight', $productVariant->getProductVariant()->getWeight());
                $rate->addPackageToShipment($package);
            }
        }


        $response = $rate->getSimpleRates();
        $data = array_pop($response);

        //if $data['rate'] isn't there then they are only ordering pop items, which the shipping for them is free.
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

//    /**
//     * @Route("/api_create_shipping_label", name="api_create_shipping_label")
//     * @param Request $request
//     */
//    public function makeShippingLabel(Request $request) {
//        $em = $this->getDoctrine()->getManager();
//        $order_id = $request->request->get('order_id');
//        $order = $em->getRepository('OrderBundle:Orders')->find($order_id);
//
//        $shipment = new \RocketShipIt\Shipment('fedex');
//
//        $shipment->setParameter('toCode', $order->getShipZip());
////        $shipment->setParameter('residentialAddressIndicator','1');
//        $shipment->setParameter('service', 'GROUND_HOME_DELIVERY');
//        $shipment->setParameter('toCompany', 'John Doe');
//        $shipment->setParameter('toName', 'John Doe');
//        $shipment->setParameter('toPhone', '1231231234');
//        $shipment->setParameter('toAddr1', '111 W Legion');
//        $shipment->setParameter('toCity', 'Knoxville');
//        $shipment->setParameter('toState', 'TN');
//        $shipment->setParameter('toCode', '37919');
//
//        $shipment->setParameter('length', '5');
//        $shipment->setParameter('width', '5');
//        $shipment->setParameter('height', '5');
//        $shipment->setParameter('weight','25');
//
//        $response = $shipment->submitShipment();
//
//        if (isset($response['error']) && $response['error'] != '') {
//            // Something went wrong, show debug information
//            echo $shipment->debug();
//        } else {
//            // Create label as a file
//            $fole = base64_decode($response['pkgs'][0]['label_img']);
//            file_put_contents('label.png', base64_decode($response['pkgs'][0]['label_img']));
//            return JsonResponse::create($response); // display response
//        }
//        return JsonResponse::create(true);
//    }



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
        $type = $request->request->get('type');


        $payment_type = $request->request->get('payment_type');
        if($payment_type == 'ledger' && $type == 'complete') {
            $ledger_service = $this->get('order.ledger');
            $ledger_service->newEntry($order->getTotal()*-1, $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "Paid for order #".$order->getOrderNumber(), 'Order', $order->getId());
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $order->setAmountPaid($order->getTotal());
            $order = $this->generateShippingLabels($order);
        }
        else if($payment_type == 'cc' && $type == 'complete') {
            $cc = $request->request->get('cc');
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $order->setAmountPaid($order->getTotal());
            // Charge CC here




            $order = $this->generateShippingLabels($order);
        }
        else if($type == 'admin' && $payment_type == '') {
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Pending'));
        }

        $order->setStatus($status);
        $order->setPaymentType($payment_type);
        $em->persist($order);
        $em->flush();
        return JsonResponse::create(true);
    }

    private function generateShippingLabels(Orders $orders) {
        $em = $this->getDoctrine()->getManager();
        $numProdVariants = 0; //count($orders->getProductVariants());
        foreach($orders->getProductVariants() as $variant)
            $numProdVariants += $variant->getQuantity();

        foreach($orders->getShippingLabels() as $label)
            $em->remove($label);

        $em->persist($orders);

        $shipmentId = '';
        $count = 0;

        foreach($orders->getProductVariants() as $variant) {
            foreach($variant->getWarehouseInfo() as $info) {
                $count++;
                $shipment = new \RocketShipIt\Shipment('fedex');

                $shipment->setParameter('toCompany', $orders->getShipName());
                $shipment->setParameter('toName', $orders->getShipName());
                $shipment->setParameter('toPhone', $orders->getShipPhone());
                $shipment->setParameter('toAddr1', $orders->getShipAddress());
                if($orders->getShipAddress2() != '')
                    $shipment->setParameter('toAddr2', $orders->getShipAddress());
                $shipment->setParameter('toCity', $orders->getShipCity());
                $shipment->setParameter('toState', $orders->getState()->getAbbreviation());
                $shipment->setParameter('toCode', $orders->getShipZip());

                /*
                 * THis needs to change once warehouses have addresses.
                 *
                 * They also need to add the fedex numbers of Distributors when applicable..
                 */
                $shipment->setParameter('shipAddr1', '7505 Lawford Rd.');
                $shipment->setParameter('shipCity', 'Knoxville');
                $shipment->setParameter('shipState', 'TN');
                $shipment->setParameter('shipCode', '37919');
                $shipment->setParameter('shipPhone', '1231231234');

                $shipment->setParameter('packageCount', $numProdVariants);
                $shipment->setParameter('sequenceNumber', $count);

                if($count != 1)
                    $shipment->setParameter('shipmentIdentification', $shipmentId);

                $dimensions = explode('x', $variant->getProductVariant()->getFedexDimensions());

                $shipment->setParameter('length', $dimensions[0]);
                $shipment->setParameter('width', $dimensions[1]);
                $shipment->setParameter('height', $dimensions[2]);
                $shipment->setParameter('weight', $variant->getProductVariant()->getWeight());

                try {
                    $response = $shipment->submitShipment();
                }
                catch(\Exception $e) {
                    return JsonResponse::create(false);
                }

                if($count == 1)
                    $shipmentId = $response['trk_main'];

                $path = 'uploads/shipping/'.$response['pkgs'][0]['pkg_trk_num'].'.png';
                file_put_contents($path, base64_decode($response['pkgs'][0]['label_img']));

                $orderShippingLabel = new OrdersShippingLabel();
                $orderShippingLabel->setPath($path);
                $orderShippingLabel->setOrder($orders);
                $orderShippingLabel->setTrackingNumber($response['pkgs'][0]['pkg_trk_num']);
                $em->persist($orderShippingLabel);

                $orders->getShippingLabels()->add($orderShippingLabel);
                $em->persist($orders);

                if($count == $numProdVariants) {
                    $charges = $response['charges'];
                    $orders->setEstimatedShipping($orders->getShipping());
                    $orders->setShipping($charges);
                }

            }
        }

        $em->flush();
        return $orders;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     *
     * @Route("/api_mark_part_of_order_shipped", name="api_mark_part_of_order_shipped")
     */
    public function markPartOfOrderShipped(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $order_id = $request->request->get('order_id');
        $order = $em->getRepository('OrderBundle:Orders')->find($order_id);

        $warehouse_id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

        //get the warehouse specific order data
        $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order, $warehouse);

        foreach($product_data as $prod) {
            foreach($prod as $item) {
                $order_warehouse_info = $em->getRepository('OrderBundle:OrdersWarehouseInfo')->find($item['id']);
                $order_warehouse_info->setShipped(1);
                $em->persist($order_warehouse_info);
            }
        }

        //check to see if the whole order is shipped and change the status on the order to Shipped if so.
        //get the order data for entire order
        $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order);
        $is_shipped = true;

        foreach($product_data as $prod) {
            foreach($prod as $item)
                if($item['shipped'] == false) {
                    $is_shipped = false;
                    break;
                }
        }

        if($is_shipped == true) {
            $status = $em->getRepository("WarehouseBundle:Status")->findOneBy(array('name' => 'Shipped'));
            $order->setStatus($status);
            $em->persist($order);
        }

        $em->flush();


        return JsonResponse::create(true);
    }
}

