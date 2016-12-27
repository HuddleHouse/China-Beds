<?php

namespace OrderBundle\Controller;

use AppBundle\Entity\User;
use OrderBundle\Entity\Orders;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\ChannelType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WarehouseBundle\Entity\Warehouse;

/**
 * Channel controller.
 *
 * @Route("/order")
 */
class OrderProductsController extends Controller
{

    /**
     *
     * @Route("/", name="my_orders_index")
     * @Method("GET")
     */
    public function getOrdersIndex()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $orders = $em->getRepository('AppBundle:User')->getLatestOrdersForUser($this->getUser());

        return $this->render('@Order/OrderProducts/my-orders.html.twig', array(
            'orders' => $orders,
            'pending' => '',

        ));
    }

    /**
     *
     * @Route("/pending", name="my_pending_orders_index")
     * @Method("GET")
     */
    public function getPendingOrdersIndex()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $orders = $em->getRepository('OrderBundle:Orders')->findBy(array('isShippable' => false, 'isPaid' => false, 'submitted_for_user' => $user, 'channel' => $this->getUser()->getActiveChannel()->getId()));
        $channel = $this->getUser()->getActiveChannel();

        return $this->render('@Order/OrderProducts/my-orders.html.twig', array(
            'orders' => $orders,
            'pending' => ' Pending',
        ));
    }

    /**
     *
     * @Route("/open", name="my_open_orders_index")
     * @Method("GET")
     */
    public function getOpenOrdersIndex()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $orders = $em->getRepository('OrderBundle:Orders')->findBy(array('isShippable' => true, 'isPaid' => false, 'submitted_for_user' => $user,'channel' => $this->getUser()->getActiveChannel()->getId()));

        $the_orders = [];
        foreach($orders as $order) {
            if ( $order->getBalance() > 0 ) {
                $the_orders[] = $order;
            }
        }

        return $this->render('@Order/OrderProducts/my-orders.html.twig', array(
            'orders' => $the_orders,
            'pending' => ' Open'
        ));
    }

    /**
     *
     * @Route("/new", name="order_new", options={"expose"=true})
     * @Method("GET")
     */
    public function newAction() {
        $em = $this->getDoctrine()->getEntityManager();

        return $this->render('@Order/OrderProducts/order-index-new.html.twig', [
            'channel'   => $this->getUser()->getActiveChannel(),
            'states'    => $em->getRepository('AppBundle:State')->findAll()
        ]);
    }

    /**
     *
     * @Route("/products", name="order_products_index", options={"expose"=true})
     * @Route("/{id}/products", name="order_products_index_old")
     * @Method("GET")
     */
    public function orderProductsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();


        $channel = $em->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());

        $user = $this->getUser();
        $user_channels = $user->getUserChannelsArray();
        if(count($user->getPriceGroups()) != 0)
            $categories = $em->getRepository('InventoryBundle:Category')->findAll();
        else
            $categories = $em->getRepository('InventoryBundle:Category')->findBy(array('name' => 'POP'));

        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user, null, null, 1);
        else
            $this->redirectToRoute('404');


        $pop = $em->getRepository('InventoryBundle:PopItem')->findBy(['channel' => $channel, 'is_hide_on_order' => 0, 'active' => 1]);
        $data = array();

        if(isset($pop)){
            foreach($pop as $popitem) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_pop_inventory i
		where i.pop_item_id = :pop_item_id");
                $statement->bindValue('pop_item_id', $popitem->getId());
                $statement->execute();
                $quantity_data = $statement->fetch();
                $quantity = (int)$quantity_data['quantity'];

                $data[] = array(
                    'id' => $popitem->getId(),
                    'cost' => $popitem->getPricePer(),
                    'name' => $popitem->getName(),
                    'description' => $popitem->getDescription(),
                    'picture' => '/uploads/documents/' . $popitem->getPath(),
                    'type' => 'pop',
                    'inventory' => $quantity //get actually inventory one day
                );
            }
        }else{
            $data[] = array(
                'id' => '',
                'cost' => '',
                'name' => '',
                'description' => '',
                'picture' => '',
                'type' => '',
                'inventory' => ''
            );
        }

        //

        $user_warehouses = [];
        foreach($user->getWarehouses() as $warehouse) {
            $user_warehouses[] = [
                'id'    => $warehouse->getId(),
                'name'  => $warehouse->getName()
            ];
        }
//        if ( $user->getWarehouse1() ) {
//            $user_warehouses[] = array('id' => $user->getWarehouse1()->getId(), 'name' => $user->getWarehouse1()->getName());
//        }
//        if ( $user->getWarehouse2() ) {
//            $user_warehouses[] = array('id' => $user->getWarehouse2()->getId(), 'name' => $user->getWarehouse2()->getName());
//        }
//        if ( $user->getWarehouse3()) {
//            $user_warehouses[] = array('id' => $user->getWarehouse3()->getId(), 'name' => $user->getWarehouse3()->getName());
//        }


        $states = $em->getRepository('AppBundle:State')->findAll();
//
//        if($user->hasRole('ROLE_DISTRIBUTOR'))
//            $user_retailers = $user->getRetailers();
//        else if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SALES_REP') || $user->hasRole('ROLE_SALES_MANAGER'))
//            $user_retailers = $em->getRepository('AppBundle:User')->findUsersByChannel($channel);
//        else
//            $user_retailers = [];

        $user_retailers = $em->getRepository('AppBundle:User')->findUsersForUser($this->getUser());

        return $this->render('@Order/OrderProducts/order-index.html.twig', array(
            'products' => $product_data,
            'categories' => $categories,
            'channel' => $channel,
            'warehouses' => $warehouses,
            'states' => $states,
            'user' => $user,
            'user_warehouses' => $user_warehouses,
            'user_retailers' => $user_retailers,
            'pop' => $data,
            'is_edit' => 0
      ));
    }


    /**
     *
     * @Route("/{id_channel}/products/{id_order}/review", name="order_products_review")
     *
     * @ParamConverter("channel", class="InventoryBundle:Channel", options={"id" = "id_channel"})
     * @ParamConverter("order", class="OrderBundle:Orders", options={"id" = "id_order"})
     *
     * @Method("GET")
     */
    public function renderOrderReviewAction(Channel $channel, Orders $order)
    {
        $em = $this->getDoctrine()->getEntityManager();


        if($em->getRepository('AppBundle:User')->canViewOrder($order, $this->getUser()) == 1) {
            $user = $this->getUser();
            $user_channels = $user->getUserChannelsArray();

            $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order);

            $groups = $user->getGroupsArray();
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

            return $this->render('@Order/OrderProducts/view-order.html.twig', array(
                'channel' => $channel,
                'order' => $order,
                'user' => $user,
                'product_data' => $product_data,
                'is_retail' => $is_retail,
                'is_dis' => $is_dis,
                'pop_items' => $pop,
                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
                'manual_items' => $manualItems = $order->getManualItems(),
                'manual_items_count' => count($manualItems = $order->getManualItems())
            ));
        }
        else
            return $this->redirectToRoute('404');
    }

    /**
     *
     * @Route("/{id_channel}/manual_order", name="manual_order")
     *
     * @ParamConverter("channel", class="InventoryBundle:Channel", options={"id" = "id_channel"})
     *
     * @Method("GET")
     */
    public function manualOrderAction(Channel $channel)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $warehouses = $this->getDoctrine()->getRepository('WarehouseBundle:Warehouse')->findByChannel(array($this->getUser()->getActiveChannel()));
        $warehouseArray = array();
        $popWarehouseArray = array();
        $productArray = array();
        $popItemArray = array();
        foreach($warehouses as $warehouse) {
            if($warehouse->isActive()){
                /** @var \WarehouseBundle\Entity\Warehouse $warehouse */
                /** @var \WarehouseBundle\Entity\WarehouseInventory $inventory */
                $wid = $warehouse->getId();
                $warehouseArray[$wid]['id'] = $wid;
                $warehouseArray[$wid]['name'] = $warehouse->getName();
                foreach ($warehouse->getInventory() as $inventory) {
                    $productArray[$wid][$inventory->getProductVariant()->getId()] = array(
                        'id' => $inventory->getProductVariant()->getId(),
                        'product' => $inventory->getProductVariant()->getProduct()->getName(),
                        'variant' => $inventory->getProductVariant()->getName(),
                        'quantity' => $inventory->getQuantity()
                    );
                    if(!$warehouse->getPopItems()->isEmpty()) {
                        $popWarehouseArray[$wid]['id'] = $wid;
                        $popWarehouseArray[$wid]['name'] = $warehouse->getName();
                        foreach($warehouse->getPopItems() as $popItem) {
                            /** @var \InventoryBundle\Entity\PopItem $popItem */
                            $popItemArray[$wid][$popItem->getId()] = array(
                                'id' => $popItem->getId(),
                                'product' => $popItem->getName(),
                            );
                        }
                    }
                }
            }
        }

        $distributors = $em->getRepository('AppBundle:User')->getAllDistributorsArray($this->getUser()->getActiveChannel());
        $retailers = $em->getRepository('AppBundle:User')->getAllRetailersArray($this->getUser()->getActiveChannel());

        $users = array();
        /** @var User $user */
        foreach($distributors as $user) {
            $users[$user->getId()] = array(
                'name' => $user->getDisplayName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'zip' => $user->getZip(),
                'address' => $user->getAddress1(),
                'address2' => $user->getAddress2(),
                'city' => $user->getCity(),
                'state' => strval($user->getState() ? $user->getState()->getId() : 0)
            );
        }

        foreach($retailers as $user) {
            $users[$user->getId()] = array(
                'name' => $user->getDisplayName(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'zip' => $user->getZip(),
                'address' => $user->getAddress1(),
                'address2' => $user->getAddress2(),
                'city' => $user->getCity(),
                'state' => strval($user->getState() ? $user->getState()->getId() : 0)
            );
        }

        return $this->render('@Order/OrderProducts/manual-order.html.twig', array(
            'channel' => $channel,
            'warehouses' => $warehouseArray,
            'products' => $productArray,
            'popWarehouses' => $popWarehouseArray,
            'popItems' => $popItemArray,
            'distributors' => $distributors,
            'retailers' => $retailers,
            'users' => $users,
            'states' => $em->getRepository('AppBundle:State')->findAll()
        ));
    }

    /**
     *
     * @Route("/{id_order}/warehouse-review/{id_warehouse}/", name="order_products_warehouse_review")
     *
     * @ParamConverter("warehouse", class="WarehouseBundle:Warehouse", options={"id" = "id_warehouse"})
     * @ParamConverter("order", class="OrderBundle:Orders", options={"id" = "id_order"})
     *
     * @Method("GET")
     */
    public function renderOrderWarehouseReviewAction(Orders $order, Warehouse $warehouse)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $channel = $order->getChannel();

        if($em->getRepository('AppBundle:User')->canViewOrder($order, $this->getUser()) == 1) {
            $user = $this->getUser();
            $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order, $warehouse);

            $groups = $user->getGroupsArray();
            $is_dis = $is_retail = 0;

            if(isset($groups['Retailer']))
                $is_retail = 1;
            if(isset($groups['Distributor']))
                $is_dis = 1;
            $pop = $order->getPopItems();

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

            return $this->render('@Order/OrderProducts/warehouse-review.html.twig', array(
                'channel' => $channel,
                'order' => $order,
                'user' => $user,
                'product_data' => $product_data,
                'is_retail' => $is_retail,
                'is_dis' => $is_dis,
                'pop_items' => $pop,
                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
                'shipped_status' => $shipped_status,
                'manual_items' => $manualItems = $order->getManualItems(),
                'manual_items_count' => count($manualItems = $order->getManualItems())
            ));
        }
        else
            return $this->redirectToRoute('404');
    }

    /**
     *
     * @Route("/{id_channel}/products/{id_order}/edit", name="order_products_edit")
     *
     * @ParamConverter("channel", class="InventoryBundle:Channel", options={"id" = "id_channel"})
     * @ParamConverter("order", class="OrderBundle:Orders", options={"id" = "id_order"})
     *
     * @Method("GET")
     */
    public function renderOrderEditAction(Channel $channel, Orders $order)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $user_channels = $user->getUserChannelsArray();
        if(count($user->getPriceGroups()) != 0)
            $categories = $em->getRepository('InventoryBundle:Category')->findAll();
        else
            $categories = $em->getRepository('InventoryBundle:Category')->findBy(array('name' => 'POP'));

        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user, null, null, 1);
        else
            $this->redirectToRoute('404');

        $path = '/uploads/documents/';
        $pop = $em->getRepository('InventoryBundle:PopItem')->getAllPopItemsArrayForCart($this->getUser()->getActiveChannel(), $path);

        foreach($user->getWarehouses() as $warehouse) {
            $user_warehouses[] = [
                'id'    => $warehouse->getId(),
                'name'  => $warehouse->getName()
            ];
        }

        $states = $em->getRepository('AppBundle:State')->findAll();

        if($user->hasRole('ROLE_DISTRIBUTOR'))
            $user_retailers = $user->getRetailers();
        else if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SALES_REP') || $user->hasRole('ROLE_SALES_MANAGER'))
            $user_retailers = $em->getRepository('AppBundle:User')->findUsersByChannel($channel);
        else
            $user_retailers = null;

        $order_variants = $order_pop = array();

        foreach($order->getPopItems() as $popItem)
            $order_pop[] = array(
                'id' => $popItem->getPopItem()->getId(),
                'quantity' => $popItem->getQuantity(),
                'price' => $popItem->getPrice()
            );

        foreach($order->getProductVariants() as $productVariant)
            $order_variants[] = array(
                'id' => $productVariant->getProductVariant()->getId(),
                'quantity' => $productVariant->getQuantity(),
                'price' => $productVariant->getPrice()
            );

        return $this->render('@Order/OrderProducts/order-index.html.twig', array(
            'products' => $product_data,
            'categories' => $categories,
            'channel' => $channel,
            'warehouses' => $warehouses,
            'states' => $states,
            'user' => $user,
            'user_warehouses' => $user_warehouses,
            'user_retailers' => $user_retailers,
            'pop' => $pop,
            'order' => $order,
            'order_variants' => $order_variants,
            'order_pop' => $order_pop,
            'is_edit' => 1,
            'num_items' => $order->getNumItems(),
            'total' => $order->getTotal()
        ));
    }


    /**
     * @Route("/delete/{id_channel}/{id_order}", name="order_products_delete")
     */
    public function deleteOrderAction(Channel $channel, Orders $order)
    {
        $that = $channel;
        $that2 = $order;
        $that3 = "something";

    }

}
