<?php

namespace OrderBundle\Controller\API;

use AppBundle\Entity\User;
use AppBundle\Services\EmailService;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Ledger;
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
     * @Route("/api_get_data_for_order_form", name="api_get_data_for_order_form")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function getDataForOrderForm(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user_id = $request->get('user_id');

        $warehouse_id = $request->get('warehouse_id');

        $return = [];
        if ( $user = $em->getRepository('AppBundle:User')->find($user_id) ) {
            $user_users = $em->getRepository('AppBundle:User')->findUsersForUser($user);


            $return = $user->toArray();
            $return['products'] = $this->getProductsForOrderFormByUser($user, $this->getUser()->getActiveChannel());
            foreach ($user_users as $user) {
                $user_data = $user->toArray();
//                $user_data['products'] = $this->getProductsForOrderFormByUser($user, $this->getUser()->getActiveChannel());
                $return['users'][$user->getRoleString()][] = $user_data;
            }
        }

        return new JsonResponse($return, 200);

    }

    private function getProductsForOrderFormByUser(User $user, Channel $channel) {
        $em = $this->getDoctrine()->getManager();

        $pops = [];
        $pop_items = $em->getRepository('InventoryBundle:PopItem')->findBy(['active' => true, 'channel' => $this->getUser()->getActiveChannel(), 'is_hide_on_order' => false]);
        foreach($pop_items as $pop_item) {
            $pops[] = $pop_item->toArray();
        }

        $variants = [];
        $products = $em->getRepository('InventoryBundle:Product')->getAllActiveProductsForChannel($channel, true);
        foreach($products as $product) {
            $variants[$product->getId()] = $product->toArray();
            $variants[$product->getId()]['variants'] = [];
        }


        foreach($user->getPriceGroups() as $price_group) {
            if ( $price_group->getChannel()->getId() == $channel->getId() ) {
                foreach($price_group->getPrices() as $price) {
                    $vid = $price->getProductVariant()->getId();
                    $pid = $price->getProductVariant()->getProduct()->getId();
                    if ( !isset($variants[$pid]) ) {
                        continue;
                        $variants[$pid] = $price->getProductVariant()->getProduct()->toArray();
                    }
                    if ( !isset($variants[$pid]['variants'][$vid]) || $variants[$pid]['variants'][$vid]['price'] > $price->getPrice() ) {
                        $variants[$pid]['variants'][$vid] = [
                            'price' => $price->getPrice(),
                            'variant' => $price->getProductVariant()->toArray($user)
                        ];
                    }
                }
            }
        }
        $variants = array_values($variants);
        foreach($variants as $k => $product) {
            if ( isset($variants[$k]['variants']) ) {
                $variants[$k]['variants'] = array_values($variants[$k]['variants']);
            }
        }
        return [
            'pop_items' => array_values($pops),
            'products'  => array_values($variants)
        ];
    }

    /**
     * @Route("/api_save_products_order_form", name="api_save_products_order_form")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function saveProductsOrderForm(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel = $em->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());
        $products = $request->request->get('products');
        $pop = $request->request->get('pop');
        $cart = $request->request->get('cart');
        $total = $request->request->get('total');
        $order_id = $request->request->get('order_id');
        $info = $request->request->get('form_info');
        $ship_to_user_id = $request->request->get('ship_to_user_id');
        if ( !$ship_to_user_id ) {
            $ship_to_user_id = $cart['dist_ship'];
        }
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
//        $this->saveManualItems($cart, $order);

        $status = $em->getRepository('WarehouseBundle:Status')->getStatusByName(Orders::STATUS_DRAFT);
        $order->setStatus($status);
        $order->setChannel($channel);
        $order->setSubmittedByUser($this->getUser());
        if($this->getUser()->getId() == $ship_to_user_id)
            $order->setSubmittedForUser($this->getUser());
        else
            $order->setSubmittedForUser($em->getRepository('AppBundle:User')->find($ship_to_user_id));

//        if ( $order->getSubmittedByUser()->hasRole('ROLE_ADMIN') ) {
//            $order->setIsShippable(true);
//        }

        $state = $em->getRepository('AppBundle:State')->find($info['state']);
        $order->setState($state);

        if($products != null) {
            foreach($products as $product) {
                if(isset($product['variants'])) {
                    foreach($product['variants'] as $variant) {
                        if(isset($variant['variant_id']) && isset($cart['variant'][$variant['variant_id']]['quantity']))
                            $quantity = $cart['variant'][$variant['variant_id']]['quantity'];
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


        if ( isset($cart['pop']) ) {
            foreach ($cart['pop'] as $popitem) {
                if ( !is_array($popitem) ) { continue;}
                if ($popitem['quantity'] > 0) {
                    $pop_item = $em->getRepository('InventoryBundle:PopItem')->find($popitem['variant_id']);
                    $orders_pop_item = new OrdersPopItem();
                    $orders_pop_item->setOrder($order);
                    $orders_pop_item->setPrice($popitem['cost']);
                    $orders_pop_item->setQuantity($popitem['quantity']);
                    $orders_pop_item->setPopItem($pop_item);
                    $em->persist($orders_pop_item);
                    $order->getPopItems()->add($orders_pop_item);
                }
            }
        }
        if ( !$order->getIsPickUp()) {
            $shipping = $this->calculateShipping($order);
            $order->setShipping($shipping['rate']);
            $order->setShipCode($shipping['service_code']);
            $order->setShipDescription($shipping['desc']);
        } else {
            $order->setShipping(null);
            $order->setShipCode(null);
            $order->setShipDescription(null);
        }

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
                $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => Orders::STATUS_SHIPPED));
            else
                $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => Orders::STATUS_READY_TO_SHIP));


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

        $warehouses = [];
        foreach($user->getWarehouses($this->getUser()->getActiveChannel()) as $warehouse) {
            $warehouses[] = [
                'name'  => $warehouse->getName(),
                'id'    =>  $warehouse->getId()
            ];
        }

        $data = array(
            'ship_name' => $user->getFullName(),
            'address' => $user->getAddress1(),
            'address2' => $user->getAddress2(),
            'city' => $user->getCity(),
            'state' => (string)$user->getState()->getId(),
            'zip' => $user->getZip(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'products' => $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($this->getUser()->getActiveChannel(), $user, null, null, 1),
            'warehouses' => $warehouses
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
        $config = new RocketShipIt\Config();
        $config->setDefault('fedex', 'key', $order->getChannel()->getFedexKey());
        $config->setDefault('fedex', 'password', $order->getChannel()->getFedexPassword());
        $config->setDefault('fedex', 'accountNumber', $order->getChannel()->getFedexNumber());
        $config->setDefault('fedex', 'meterNumber', $order->getChannel()->getFedexMeterNumber());

        $rate = new \RocketShipIt\Rate('fedex', ['config' => $config]);
        $rate->setParameter('accountNumber', $order->getChannel()->getFedexNumber());
//        $rate->setParameter('key', $order->getChannel()->getFedexKey());
//        $rate->setParameter('password', $order->getChannel()->getFedexPassword());
        $rate->setParameter('meterNumber', $order->getChannel()->getFedexMeterNumber());

        $rate->setParameter('residentialAddressIndicator','1');
        $rate->setParameter('service', 'FEDEX_GROUND');

        foreach($order->getProductVariants() as $productVariant) {
            foreach($productVariant->getWarehouseInfo() as $info) {
                $rate->setParameter('toCode', $order->getShipZip());
                $rate->setParameter('shipCode', $info->getWarehouse()->getZip());

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


    private function validateCreditPayment(Orders $order, &$payment) {
        if ( empty($payment['amount']) ) { throw new \Exception('Amount cannot be empty.'); }
        if ( $payment['amount'] < 0 ) { throw new \Exception('Amount cannot be less than 0.'); }
        if ( $order->getSubmittedForUser()->getLedgerTotal() < $payment['amount'] ) { throw new \Exception('Ledger Total is less than Payment Amount'); }
        return true;
    }

    private function validateCreditCardPayment(Orders $order, &$payment) {
        if ( empty($payment['amount']) ) { throw new \Exception('Amount cannot be empty.'); }
        if ( $payment['amount'] < 0 ) { throw new \Exception('Amount cannot be less than 0.'); }

        $result = $this->get('authorize.net')->chargeCreditCard([
            'amount'        => $payment['amount'],
            'number'        => $payment['number'],
            'expiry-month'  => $payment['expires_month'],
            'expiry-year'   => $payment['expires_year'],
            'cvv'           => $payment['cvv'],
            'order_id'      => $order->getOrderId()
        ], 'authOnlyTransaction');

        if ( $result['success'] ) {
            return $payment['trans_id'] = $result['trans_id'];
            return true;
        } else {
            throw new \Exception($result['error_message']);
        }

    }

    private function validateAchPayment(Orders $order, &$payment) {
        if ( empty($payment['amount']) ) { return ['error' => true, 'message' => 'Amount cannot be empty.']; }
        if ( $payment['amount'] < 0 ) { return ['error' => true, 'message' => 'Amount cannot be less than 0.']; }
        return true;
    }

    private function processCreditPayment(Orders &$order, $payment) {
        $ledger_service = $this->get('ledger.service');
        $ledger = $ledger_service->newEntry($payment['amount']*-1, $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "Paid for order using credit balance.", Ledger::TYPE_ORDER, $order->getId(), false, true, false);

        $order_payment = new OrderPayment();
        $order_payment->setMethod($payment['method']);
        $order_payment->setAmount($payment['amount']);
        $order_payment->setLedger($ledger);

        $order->addOrderPayment($order_payment);

        return true;
    }

    private function processCreditCardPayment(Orders &$order, $payment) {
        if ( empty($payment['amount']) ) { return ['error' => true, 'message' => 'Amount cannot be empty.']; }
        if ( $payment['amount'] < 0 ) { return ['error' => true, 'message' => 'Amount cannot be less than 0.']; }

        $result = $this->get('authorize.net')->chargeCreditCard([
            'amount'        => $payment['amount'],
            'number'        => $payment['number'],
            'expiry-month'  => $payment['expires_month'],
            'expiry-year'   => $payment['expires_year'],
            'cvv'           => $payment['cvv'],
            'order_id'      => $order->getOrderId(),
            'trans_id'      => $payment['trans_id']
        ], 'priorAuthCaptureTransaction');

        $order_payment = new OrderPayment();
        $order_payment->setMethod($payment['method']);
        $order_payment->setAmount($payment['amount']);
        $order_payment->setGatewayAuthCode($result['auth_code']);
        $order_payment->setGatewayTransactionId($result['trans_id']);
        $order_payment->setDetail(substr($payment['number'], -4));

        $order->addOrderPayment($order_payment);

        if ( $result['success'] ) {
            return true;
        } else {
            return [
                'error'     => true,
                'message'     => $result['error_message']
            ];
        }

    }

    private function processAchPayment(Orders &$order, $payment) {
        $ledger_service = $this->get('ledger.service');
        // first we need to create one for the debit
        $ledger = $ledger_service->newEntry($payment['amount'], $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "ACH transfer for order", Ledger::TYPE_PAYMENT, $order->getId(), false, false, false);
        $order->addLedger($ledger);

        // second create one for the credit
        $ledger = $ledger_service->newEntry($payment['amount']*-1, $order->getSubmittedForUser(), $order->getSubmittedForUser(), $order->getChannel(), "Paid for order using ach transfer", Ledger::TYPE_ORDER, $order->getId(), false, true, false);

        $order_payment = new OrderPayment();
        $order_payment->setMethod($payment['method']);
        $order_payment->setAmount($payment['amount']);
        $order_payment->setLedger($ledger);

        $order->addOrderPayment($order_payment);

        return true;
    }

    private function validatePayments(Orders $order, &$payments = []) {
        try {
            foreach ($payments as &$payment) {
                switch ($payment['method']) {
                    case 'credit':
                        $this->validateCreditPayment($order, $payment);
                        break;
                    case 'cc':
                        $this->validateCreditCardPayment($order, $payment);
                        break;
                    case 'ach':
                        $this->validateAchPayment($order, $payment);
                        break;
                }
            }
            return true;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    private function processPayments(Orders $order, $payments = []) {
        try {
            foreach ($payments as $payment) {
                switch ($payment['method']) {
                    case 'credit':
                        $this->processCreditPayment($order, $payment);
                        break;
                    case 'cc':
                        $this->processCreditCardPayment($order, $payment);
                        break;
                    case 'ach':
                        $this->processAchPayment($order, $payment);
                        break;
                }
            }
            return true;
        } catch(\Exception $e) {
            throw $e;
        }
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


        $payments = $request->request->get('payments');


        try {
            $this->validatePayments($order, $payments);
            $this->processPayments($order, $payments);
            $order->setIsShippable(true);
            $order->setIsPaid(true);

            if ( $type == 'complete' && !$order->getIsManual() ) {
                try {
                    $this->generateShippingLabels($order);
                } catch(\Exception $e) {
                    //email someone
                }

            }

            $status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Paid'));
            $order->setAmountPaid($order->getPaidTotal());
            $order->setStatus($status);


            $this->get('warehouse.warehouse_service')->modifyInventoryLevelForOrder($order);

            $order->setStatus($status);
            $em->persist($order);
            $em->flush();

            if ( !$order->getIsManual() ) {
                $this->get('email_service')->sendAdminOrderNotification($order);
                $this->get('email_service')->sendCustomerOrderNotification($order);
                $this->get('email_service')->sendWarehouseOrderNotification($order);
            }

            return new JsonResponse(['success' => true, 'error_message' => null]);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error_message' => $e->getMessage()]);
        }
    }

    /**
     * @param Orders $orders
     * @return Orders
     */
    private function generateShippingLabels(Orders &$orders) {
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

                    $shipment->setParameter('accountNumber', $orders->getChannel()->getFedexNumber());
                    $shipment->setParameter('key', $orders->getChannel()->getFedexKey());
                    $shipment->setParameter('password', $orders->getChannel()->getFedexPassword());
                    $shipment->setParameter('meterNumber', $orders->getChannel()->getFedexMeterNumber());
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


//                    if ($orders->getSubmittedForUser()->getDistributorFedexNumber(
//                        ) != null || $orders->getSubmittedForUser()->getDistributorFedexNumber() != ''
//                    ) {
//                        $shipment->setParameter('paymentType', 'THIRD_PARTY');
//                        $shipment->setParameter(
//                            'thirdPartyAccount',
//                            $orders->getSubmittedForUser()->getDistributorFedexNumber()
//                        );
//                    }

                    $response = $shipment->submitShipment();

                    if (isset($response['trk_main'])) {
                        if ($count == 1) {
                            $shipmentId = $response['trk_main'];
                        }
                    } else {
                        throw new \Exception($response['error']);

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
                        $info->addShippingLabel($orderShippingLabel);
                    }
//                    $em->persist($orders);

                    if ($count == $numProdVariants) {
                        $charges = $response['charges'];
                        $orders->setEstimatedShipping($orders->getShipping());
                        $orders->setShipping($charges);
                    }

                }
            }
        }

//        $em->flush();
//        return $orders;
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

    /**
     * @param Request $request
     *
     * @Route("/api_get_user_info_manual_order", name="api_get_user_info_manual_order")
     * @return JsonResponse
     */
    public function userWarehouseInfoAction(Request $request)
    {
        $warehouses = $this->getDoctrine()->getRepository('WarehouseBundle:Warehouse')->findByChannels(array($this->getUser()->getActiveChannel()));
        $warehouseArray = array();
        foreach($warehouses as $warehouse) {
            /** @var \WarehouseBundle\Entity\Warehouse $warehouse */
            /** @var \WarehouseBundle\Entity\WarehouseInventory $inventory */
            $wid = $warehouse->getId();
            $warehouseArray[$wid]['id'] = $wid;
            $warehouseArray[$wid]['name'] = $warehouse->getName();
            foreach ($warehouse->getInventory() as $inventory) {
                $warehouseArray[$wid][$inventory->getProductVariant()->getId()] = array(
                    'id' => $inventory->getProductVariant()->getId(),
                    'product' => $inventory->getProductVariant()->getProduct()->getName(),
                    'variant' => $inventory->getProductVariant()->getName(),
                    'quantity' => $inventory->getQuantity()
                );
            }
        }

        return new JsonResponse($warehouseArray);
    }

    /**
     * @Route("/api_save_manual_order_form_pdf", name="api_save_manual_order_form_pdf")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveManualOrderFormPDF(Request $request)
    {
        try {
            $order = $this->getDoctrine()->getRepository('OrderBundle:Orders')->find($request->get('order_id'));
            $file = $request->files->get('pdf');
            $order->setFile($file);
            $order->upload();
            $this->getDoctrine()->getEntityManager()->persist($order);
            $this->getDoctrine()->getEntityManager()->flush();
            return new JsonResponse(array(true));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

    /**
     * @Route("/api_save_manual_order_form", name="api_save_manual_order_form")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveManualOrderForm(Request $request)
    {
        try {
            //initialize things
            $em = $this->getDoctrine()->getManager();
            $channel = $em->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());
            $products = array();
            $pop = array();
            $warehouses = array();
            $info = array();
            $product_index = 0;
            $pop_index = 0;

            /*
             * organize the data from the form....just go with it
             * we'll have arrays for products, pop items, and the rest of the info
             * the exceptions to the rule are eta date and pickup date
             */
            foreach ($request->get('form') as $item) {
                if (strpos($item['name'], 'products[') !== false) {
                    if (strpos($item['name'], '[warehouse]') !== false)
                        $products[$product_index]['warehouse'] = $em->getRepository('WarehouseBundle:Warehouse')->find($item['value']);
                    elseif (strpos($item['name'], '[product]') !== false)
                        $products[$product_index]['product'] = $em->getRepository('InventoryBundle:ProductVariant')->find($item['value']);
                    elseif (strpos($item['name'], '[unit_cost]') !== false)
                        $products[$product_index]['unit_cost'] = $item['value'];
                    elseif (strpos($item['name'], '[qty]') !== false)
                        $products[$product_index]['qty'] = $item['value'];
                    elseif (strpos($item['name'], '[subtotal]') !== false) {
                        $products[$product_index]['subtotal'] = $item['value'];
                        $product_index++;
                    }
                } elseif (strpos($item['name'], 'pop[') !== false) {
                    if (strpos($item['name'], '[warehouse]') !== false)
                        $pop[$pop_index]['warehouse'] = $em->getRepository('WarehouseBundle:Warehouse')->find($item['value']);
                    elseif (strpos($item['name'], '[product]') !== false)
                        $pop[$pop_index]['product'] = $em->getRepository('InventoryBundle:PopItem')->find($item['value']);
                    elseif (strpos($item['name'], '[unit_cost]') !== false)
                        $pop[$pop_index]['unit_cost'] = $item['value'];
                    elseif (strpos($item['name'], '[qty]') !== false)
                        $pop[$pop_index]['qty'] = $item['value'];
                    elseif (strpos($item['name'], '[subtotal]') !== false) {
                        $pop[$pop_index]['subtotal'] = $item['value'];
                        $pop_index++;
                    }
                } else {
                    $info[$item['name']] = $item['value'];
                }
            }

            if ($info['isPickup'] == 'false')
                $order = new Orders(array(
                    'po' => $info['poNumber'],
                    'comments' => $info['comments'],
                    'ship' => 'true',
                    'ship_name' => $info['shipName'],
                    'address' => $info['shipAddress'],
                    'address2' => $info['shipAddress2'],
                    'city' => $info['shipCity'],
                    'zip' => $info['shipZip'],
                    'phone' => $info['shipPhone'],
                    'email' => $info['shipEmail']
                ));
            else
                $order = new Orders(array(
                    'po' => $info['poNumber'],
                    'comments' => $info['comments'],
                    'pick_up' => 'true',
                    'pick_up_date' => $request->get('pickupDate'),
                    'agent_name' => $info['pickupAgent']
                ));

//        else /* if not a new order */ {
//            $order = $em->getRepository('OrderBundle:Orders')->find($order_id);
//            foreach($order->getProductVariants() as $productVariant) {
//                foreach($productVariant->getWarehouseInfo() as $item)
//                    $em->remove($item);
//                $em->remove($productVariant);
//            }
//            foreach($order->getPopItems() as $productVariant)
//                $em->remove($productVariant);
//
//            $order->setData($info);
//        }

            $order->setIsManual(true);
            $em->persist($order);
            $em->flush();

            $order->setOrderId('O-' . str_pad($order->getId(), 5, "0", STR_PAD_LEFT));
            /** @var \AppBundle\Entity\User $user */
            $user = $em->getRepository('AppBundle:User')->find($info['user']);
            $status = $em->getRepository('WarehouseBundle:Status')->getStatusByName(Orders::STATUS_READY_TO_SHIP);


            $order->setStatus($status);
            $order->setChannel($channel);
            $order->setSubmittedByUser($this->getUser());
            $order->setSubmittedForUser($user);
            $order->setState($em->getRepository('AppBundle:State')->find($info['shipState']));
            $channel->getOrders()->add($order);
            $this->getUser()->getSubmittedOrders()->add($order);
            $user->getOrders()->add($order);
            $order->setIsShippable(true);

            if ($products != null) {
                foreach ($products as $product) {
                    $quantity = intval($product['qty']);
                    if ($quantity != null && $quantity > 0) {
                        $orders_product_variant = new OrdersProductVariant();
                        $orders_product_variant->setOrder($order);
                        $orders_product_variant->setPrice($product['unit_cost']);
                        $orders_product_variant->setQuantity($quantity);
                        $orders_product_variant->setProductVariant($product['product']);

                        $warehouses[] = $product['warehouse'];
                        $warehouseQuantity = $em->getRepository('WarehouseBundle:WarehouseInventory')->findOneBy(array('warehouse' => $product['warehouse'], 'product_variant' => $product['product']));
//                        if ($quantity <= $warehouseQuantity->getQuantity())
                        $orders_warehouse_info = new OrdersWarehouseInfo($quantity, $orders_product_variant, $product['warehouse']);
//                        else //$quantity > $warehouseQuantity->getQuantity()
//                            $orders_warehouse_info = new OrdersWarehouseInfo($warehouseQuantity->getQuantity(), $orders_product_variant, $product['warehouse']);

                        $orders_product_variant->addWarehouseInfo($orders_warehouse_info);
                        $order->addProductVariants($orders_product_variant);
                        $em->persist($orders_product_variant);
                        $em->persist($orders_warehouse_info);
                    }
                }
            }

            if ($pop != null && !empty($pop)) {
                foreach ($pop as $popitem) {
                    $quantity = intval($popitem['qty']);
                    if ($quantity != null && $quantity > 0) {
                        $orders_pop_item = new OrdersPopItem();
                        $orders_pop_item->setOrder($order);
                        $orders_pop_item->setPrice($popitem['unit_cost']);
                        $orders_pop_item->setQuantity($quantity);
                        $orders_pop_item->setPopItem($popitem['product']);
                        $order->getPopItems()->add($orders_pop_item);
                        $em->persist($orders_pop_item);
                    }
                }
            }

            if ($info['isFedex'] == 'true') {
                $order->setShipping($info['fedex_cost']);
                $order->setShipCode('FEDEX_GROUND');
                $order->setShipDescription('FedEx Ground');
            } else {
                $order->setShipping($info['other_shipping_cost']);
                $order->setShipCode('OTHER');
                $order->setShipDescription('Other Shipping');
            }

            $this->get('warehouse.warehouse_service')->modifyInventoryLevelForOrder($order);
            $order->setSubmitDate(new \DateTime($request->get('orderDate')));
            $em->persist($order);
            $em->flush();

//            try {
//                $this->get('email_service')->sendAdminOrderNotification($order);
//                $this->get('email_service')->sendCustomerOrderNotification($order);
//                $this->get('email_service')->sendWarehouseOrderNotification($order);
//            } catch (\Exception $e) {
//                // @todo ignore for now.  Need to log
//            }

//            $groups = $user->getGroupsArray();
//            $is_dis = $is_retail = 0;
//
//            if (isset($groups['Retailer']))
//                $is_retail = 1;
//            if (isset($groups['Distributor']))
//                $is_dis = 1;
//            $pop = $order->getPopItems();
//
//
//            $warehouses = array_unique($warehouses);
//
//            foreach ($warehouses as $warehouse) {
//                $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order, $warehouse);
//                $is_shipped = false;
//
//                foreach ($product_data as $prod) foreach ($prod as $item)
//                    if ($item['shipped'] == true) {
//                        $is_shipped = true;
//                        break;
//                    }
//
//                if ($is_shipped == true)
//                    $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Shipped'));
//                else
//                    $shipped_status = $em->getRepository('WarehouseBundle:Status')->findOneBy(array('name' => 'Ready To Ship'));
//
//
////            $w = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);
////            $this->container->get('email_service')->sendWarehouseOrderReceipt($channel, $w, $this->renderView('@Order/OrderProducts/order-email-receipt-warehouse.html.twig', array(
////                'channel' => $channel,
////                'order' => $order,
////                'product_data' => $product_data,
////                'is_retail' => $is_retail,
////                'is_dis' => $is_dis,
////                'pop_items' => $pop,
////                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
////                'shipped_status' => $shipped_status
////            )));
//            }
            return JsonResponse::create(array('success' => true, 'order_id' => $order->getId()));
        }
        catch(\Exception $e) {
            return JsonResponse::create(array('success' => false, 'error_text' => $e->getMessage()));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api-delete-tracking", name="api-delete-tracking")
     */
    public function deleteTrackingAction(Request $request){
        $trackingId = $request->get('track-id');
        $em = $this->getDoctrine()->getManager();

        try {
            $shippingLabel = $em->getRepository('OrderBundle:OrdersShippingLabel')->find($trackingId);
            $em->remove($shippingLabel);
            $em->flush();

            return JsonResponse::create(array(true, 'Shipping Label Deleted'));
        }catch(\Exception $e){
            return JsonResponse::create(array(false, $e));
        }
    }
}

