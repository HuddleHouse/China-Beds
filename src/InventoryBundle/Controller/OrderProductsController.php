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
        $em = $this->getDoctrine()->getEntityManager();
        $categories = $em->getRepository('InventoryBundle:ProductCategory')->findAll();
        $user = $this->getUser();

        $connection = $em->getConnection();
        $statement = $connection->prepare("select p.id, p.name, p.description, p.sku, ch.name as channel_name, ch.url as channel_url, ch.id as channel_id, cat.name as category_name
	from product p
		left join product_channels c
			on c.product_id = p.id
		left join channel ch 
			on c.channel_id = ch.id
		left join product_variant v
			on p.id = v.product_id
		left join product_categories cat
			on cat.id = p.product_category_id
		where p.active = 1
		group by c.id");

        $product_data = array();

        try {
            $statement->execute();
            $products = $statement->fetchAll();

            foreach($products as $product) {
                $connection = $em->getConnection();
                $statement = $connection->prepare("select *, v.id as variant_id, p.price/100 as price
	from product_variant v 
		left join price_group_prices p 
			on p.product_variant_id = v.id
		where v.product_id = :product_id");
                $statement->bindValue('product_id', $product['id']);
                $statement->execute();
                $variants = $statement->fetchAll();

                $product['variants'] = $variants;

                $product_data[$product['channel_name']][] = $product;
            }


        } catch(\Exception $e) {
            $this->addFlash('error', 'Error loading Information: ' . $e->getMessage());
        }


        return $this->render('@Inventory/OrderProducts/index.html.twig', array(
            'products' => $product_data,
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
