<?php

namespace OrderBundle\Controller;

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
        $categories = $em->getRepository('InventoryBundle:Category')->findAll();
        $states = $em->getRepository('AppBundle:State')->findAll();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user);
        else
            $this->redirectToRoute('404');


        return $this->render('@Order/OrderProducts/order-index.html.twig', array(
            'products' => $product_data,
            'categories' => $categories,
            'channel' => $channel,
            'warehouses' => $warehouses,
            'states' => $states
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
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user);
        else
            $this->redirectToRoute('404');


        return $this->render('@Order/OrderProducts/view-order.html.twig', array(
            'channel' => $channel,
            'order' => $order,
            'user' => $user
        ));
    }

}
