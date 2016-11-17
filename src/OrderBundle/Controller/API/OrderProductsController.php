<?php

namespace OrderBundle\Controller\API;

use AppBundle\Services\EmailService;
use OrderBundle\Entity\OrderPayment;
use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersManualItem;
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
        $warehouses = array();

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

        /*
        * Save the manual Items here
        */
        $this->saveManualItems($cart, $order);

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
                                $warehouses[] = $warehouse_data['warehouse_id'];
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


        if($pop != null && !empty($pop)) {
            foreach ($pop as $popitem) {
                $quantity = 0;
                if (isset($pop_order_quan[$popitem['id']]))
                    $quantity = $pop_order_quan[$popitem['id']];
                if ($quantity > 0) {
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
        }
        $shipping = $this->calculateShipping($order);
        $order->setShipping($shipping['rate']);
        $order->setShipCode($shipping['service_code']);
        $order->setShipDescription($shipping['desc']);

        $em->persist($order);
        $em->flush();

        $groups = $this->getUser()->getGroupsArray();
        $is_dis = $is_retail = 0;

        if(isset($groups['Retailer']))
            $is_retail = 1;
        if(isset($groups['Distributor']))
            $is_dis = 1;
        $pop = $order->getPopItems();

        $manualItems = $order->getManualItems();
        $manualCount = 0;
        foreach($manualItems as $manualItem) {
            $manualCount++;
        }

//        $this->container->get('email_service')->sendOrderReceipt($channel, $order, $this->renderView('@Order/OrderProducts/order-email-receipt.html.twig', array(
//                'channel' => $channel,
//                'order' => $order,
//                'user' => $this->getUser(),
//                'product_data' => $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order),
//                'is_retail' => $is_retail,
//                'is_dis' => $is_dis,
//                'pop_items' => $pop,
//                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
//                'manual_items' => $manualItems,
//                'manual_items_count' => $manualCount
//            )
//        ));

        $warehouses = array_unique($warehouses);

        foreach($warehouses as $warehouse_id) {
            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);
            $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order, $warehouse);
            $is_shipped = false;

            foreach($product_data as $prod) {
                foreach($prod as $item)
                    if($item['shipped'] == true)
                        $is_shipped = true;
            }

            if($is_shipped == true)
                $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Shipped'));
            else
                $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Ready To Ship'));


//            $w = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);
//            $this->container->get('email_service')->sendWarehouseOrderReceipt($channel, $w, $this->renderView('@Order/OrderProducts/order-email-receipt-warehouse.html.twig', array(
//                'channel' => $channel,
//                'order' => $order,
//                'product_data' => $product_data,
//                'is_retail' => $is_retail,
//                'is_dis' => $is_dis,
//                'pop_items' => $pop,
//                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
//                'shipped_status' => $shipped_status
//            )));
        }

        return JsonResponse::create($order->getId());
    }

    /**
     * @param $cart
     * @param Orders $orders
     */
    private function saveManualItems($cart, Orders $orders){
        $em = $this->getDoctrine()->getManager();
        $count = 0;
        if(isset($cart['customItems']))
            foreach($cart['customItems'] as $item) {
                $orderManualItem = new OrdersManualItem();
                $orderManualItem->setOrder($orders);
                $orderManualItem->setDescription($item['description']);
                $orderManualItem->setPrice($item['price']);
                $em->persist($orderManualItem);
                $count++;
            }
        $em->flush();
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
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user, $warehouse, null, 1);
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
     * @param Orders $order
     * @return mixed
     *
     * calculates shipping
     */
    private function calculateShipping(Orders $order) {
        $rate = new \RocketShipIt\Rate('fedex');
        $rate->setParameter('shipCode', '37919');
        $rate->setParameter('residentialAddressIndicator','1');
        $rate->setParameter('service', 'FEDEX_GROUND');

        foreach($order->getProductVariants() as $productVariant) {
            foreach($productVariant->getWarehouseInfo() as $info) {
                $rate->setParameter('toCode', $info->getWarehouse()->getZip());

                $dimensions = explode('x', $productVariant->getProductVariant()->getFedexDimensions());
                if ( count($dimensions) == 1 ) {
                    $dimensions = explode('X', $productVariant->getProductVariant()->getFedexDimensions());
                }

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

        return $data;
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

        $order_payment = new OrderPayment();
        $order_payment->setAmount($order->getTotal());
        $order->addOrderPayment($order_payment);

        $payment_type = $request->request->get('payment_type');
        if($payment_type == 'ledger' && $type == 'complete') {
            $order = $this->generateShippingLabels($order);
            $ledger_service = $this->get('ledger.service');
            $ledger_service->newEntry($order->getTotal()*-1, $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "Paid for order #".$order->getOrderNumber(), 'Order', $order->getId());
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $order->setAmountPaid($order->getTotal());

            $order_payment->setMethod('ledger');
            $order_payment->setAmount($order->getTotal());
        }
        elseif($payment_type == 'cc' && $type == 'complete') {
            $order = $this->generateShippingLabels($order);
            $cc = $request->request->get('cc');
            $cc['amount'] = $order->getTotal();
            $cc['order_id'] = $order->getId();

            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $order->setAmountPaid($order->getTotal());
            // Charge CC here

            try {
                $result = $this->get('authorize.net')->chargeCreditCard($cc);
                if ( $result['success'] ) {
                    $order_payment->setMethod('cc');
                    $order_payment->setGatewayAuthCode($result['auth_code']);
                    $order_payment->setGatewayTransactionId($result['trans_id']);
                    $order_payment->setDetail(substr($cc['number'], -4));
                } else {
                    JsonResponse::create(new \Exception($result['error_message']));
                }
            }
            catch(\Exception $e) {
                return JsonResponse::create($e);
            }
        }
        else if($type == 'admin' && $payment_type == '') {
            $order = $this->generateShippingLabels($order);
            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Pending'));
            $order->removeOrderPayment($order_payment);
        }

        $this->get('warehouse.warehouse_service')->modifyInventoryLevelForOrder($order);

        $order->setStatus($status);
        $order->setPaymentType($payment_type);
        $em->persist($order);
        $em->flush();

        $this->get('email_service')->sendAdminOrderNotification($order);

        return JsonResponse::create(true);
    }

    /**
     * @param Orders $orders
     * @return Orders
     */
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
            for($i=0;$i<$variant->getQuantity();$i++) {
                foreach ($variant->getWarehouseInfo() as $info) {
                    $count++;
                    $shipment = new \RocketShipIt\Shipment('fedex');

                    $shipment->setParameter('toCompany', $orders->getShipName());
                    $shipment->setParameter('toName', $orders->getShipName());
                    $shipment->setParameter('toPhone', $orders->getShipPhone());
                    $shipment->setParameter('toAddr1', $orders->getShipAddress());
                    if ($orders->getShipAddress2() != '') {
                        $shipment->setParameter('toAddr2', $orders->getShipAddress2());
                    }
                    $shipment->setParameter('toCity', $orders->getShipCity());
                    $shipment->setParameter('toState', $orders->getState()->getAbbreviation());
                    $shipment->setParameter('toCode', $orders->getShipZip());

                    /*
                     * THis needs to change once warehouses have addresses.
                     *
                     * They also need to add the fedex numbers of Distributors when applicable..
                     */
                    $shipment->setParameter('shipAddr1', $info->getWarehouse()->getAddress1());
                    $shipment->setParameter('shipCity', $info->getWarehouse()->getCity());
                    $shipment->setParameter('shipState', $info->getWarehouse()->getState()->getAbbreviation());
                    $shipment->setParameter('shipCode', $info->getWarehouse()->getZip());
                    $shipment->setParameter('shipPhone', $info->getWarehouse()->getPhone());

                    $shipment->setParameter('packageCount', $numProdVariants);
                    $shipment->setParameter('sequenceNumber', $count);

                    if ($count != 1) {
                        $shipment->setParameter('shipmentIdentification', $shipmentId);
                    }

                    $dimensions = explode('x', $variant->getProductVariant()->getFedexDimensions());
                    if (count($dimensions) == 1) {
                        $dimensions = explode('X', $variant->getProductVariant()->getFedexDimensions());
                    }

                    if (isset($dimensions[0])) {
                        $shipment->setParameter('length', $dimensions[0]);
                    }
                    if (isset($dimensions[1])) {
                        $shipment->setParameter('width', $dimensions[1]);
                    }
                    if (isset($dimensions[2])) {
                        $shipment->setParameter('height', $dimensions[2]);
                    }
                    $shipment->setParameter('weight', $variant->getProductVariant()->getWeight());


                    if ($orders->getSubmittedForUser()->getDistributorFedexNumber(
                        ) != null || $orders->getSubmittedForUser()->getDistributorFedexNumber() != ''
                    ) {
                        $shipment->setParameter('paymentType', 'THIRD_PARTY');
                        $shipment->setParameter(
                            'thirdPartyAccount',
                            $orders->getSubmittedForUser()->getDistributorFedexNumber()
                        );
                    }

                    $response = $shipment->submitShipment();

                    if (isset($response['trk_main'])) {
                        if ($count == 1) {
                            $shipmentId = $response['trk_main'];
                        }
                    } else {
                        return $orders;

                    }

                    foreach ($response['pkgs'] as $pkg) {
                        $path = 'uploads/shipping/' . $pkg['pkg_trk_num'] . '.' . $pkg['label_fmt'];
                        file_put_contents(
                            $this->get('kernel')->getRootDir() . '/../web/' . $path,
                            base64_decode($pkg['label_img'])
                        );

                        $orderShippingLabel = new OrdersShippingLabel();
                        $orderShippingLabel->setPath($path);
                        $orderShippingLabel->setOrder($orders);
                        $orderShippingLabel->setTrackingNumber($pkg['pkg_trk_num']);

                        $orders->addShippingLabel($orderShippingLabel);
                        $variant->addShippingLabel($orderShippingLabel);
                    }
                    $em->persist($orders);

                    if ($count == $numProdVariants) {
                        $charges = $response['charges'];
                        $orders->setEstimatedShipping($orders->getShipping());
                        $orders->setShipping($charges);
                    }

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

