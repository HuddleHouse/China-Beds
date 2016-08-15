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
     * @Route("/products", name="order_products_index")
     * @Method("GET")
     */
    public function orderProductsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        //If user is Admin, then return all products
        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        $prduct_data = array();

        foreach($products as $product) {
            $i = 1;
        }


        return $this->render('@Inventory/OrderProducts/index.html.twig', array(
            'products' => $products,
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
