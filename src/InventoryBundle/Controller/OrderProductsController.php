<?php

namespace InventoryBundle\Controller;

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
     * @Route("/{id}/products", name="order_products_index")
     * @Method("GET")
     */
    public function orderProductsAction(Channel $channel)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $user_channels = $user->getUserChannelsArray();
        $categories = $em->getRepository('InventoryBundle:Category')->findAll();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->getAllWarehousesArray();

        if($user_channels[$channel->getId()])
            $product_data = $em->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($channel, $user);
        else
            $this->redirectToRoute('404');


        return $this->render('@Inventory/OrderProducts/index.html.twig', array(
            'products' => $product_data,
            'categories' => $categories,
            'channel' => $channel,
            'warehouses' => $warehouses
        ));
    }

    /**
     *
     * @Route("/pop", name="order_pop_index")
     * @Method("GET")
     */
    public function showAction(Channel $channel)
    {
        $deleteForm = $this->createDeleteForm($channel);

        return $this->render('@Inventory/Channel/show.html.twig', array(
            'channel' => $channel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

}
