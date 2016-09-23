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

/**
 * Channel controller.
 *
 * @Route("/order")
 */
class OrderProductsController extends Controller
{
    /**
     *
     * @Route("/{id}/products", name="order_products_index", options={"expose"=true})
     * @Method("GET")
     */
    public function orderProductsAction(Channel $channel)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $user_channels = $user->getUserChannelsArray();
        if(count($user->getPriceGroups()) != 0)
            $categories = $em->getRepository('InventoryBundle:Category')->findAll();
        else
            $categories = $em->getRepository('InventoryBundle:Category')->findBy(array('name' => 'POP'));

        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user);
        else
            $this->redirectToRoute('404');

        $pop = $em->getRepository('InventoryBundle:PopItem')->getAllPopItemsArrayForCart();

        $user_warehouses[] = array('id' => $user->getWarehouse1()->getId(), 'name' => $user->getWarehouse1()->getName());
        $user_warehouses[] = array('id' => $user->getWarehouse2()->getId(), 'name' => $user->getWarehouse2()->getName());
        $user_warehouses[] = array('id' => $user->getWarehouse3()->getId(), 'name' => $user->getWarehouse3()->getName());

        $states = $em->getRepository('AppBundle:State')->findAll();

        if($user->hasRole('ROLE_DISTRIBUTOR'))
            $user_retailers = $user->getRetailers();
        else
            $user_retailers = null;

        return $this->render('@Order/OrderProducts/order-index.html.twig', array(
            'products' => $product_data,
            'categories' => $categories,
            'channel' => $channel,
            'warehouses' => $warehouses,
            'states' => $states,
            'user' => $user,
            'user_warehouses' => $user_warehouses,
            'user_retailers' => $user_retailers,
            'states' => $states,
            'pop' => $pop
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
        $user = $this->getUser();
        $user_channels = $user->getUserChannelsArray();

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order);
        else
            $this->redirectToRoute('404');

        $groups = $user->getGroupsArray();
        $is_dis = $is_retail = 0;

        if(isset($groups['Retailer']))
            $is_retail = 1;
        if(isset($groups['Distributor']))
            $is_dis = 1;

        return $this->render('@Order/OrderProducts/view-order.html.twig', array(
            'channel' => $channel,
            'order' => $order,
            'user' => $user,
            'product_data' => $product_data,
            'is_retail' => $is_retail,
            'is_dis' => $is_dis
        ));
    }

}
