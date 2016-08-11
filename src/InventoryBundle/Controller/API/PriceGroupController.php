<?php

namespace InventoryBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class PriceGroupController extends Controller
{

    /**
     * @Route("/api_get_all_price_group_products", name="api_get_all_price_group_products")
     */
    public function getAllPriceGroupProductsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        $product_data = array();
        $price_group_id = $request->request->get('price_group_id');

        foreach($products as $product) {
            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT price FROM price_group_prices WHERE product_id = :product_id AND price_group_id = :price_group_id");
            $statement->bindValue('product_id', $product->getId());
            $statement->bindValue('price_group_id', $price_group_id);

            try {
                $statement->execute();
                $price = $statement->fetch();
                if($price == false)
                    $price = 0;

                $product_data[] = array(
                    'name' => $product->getName(),
                    'id' => $product->getId(),
                    'price' => $price
                );
            } catch(\Exception $e) {
                return JsonResponse::create(false);
            }
        }

        return JsonResponse::create($product_data);
    }


    /**
     * @Route("/api_update_product_price", name="api_update_product_price")
     */
    public function updateAllProductPrices(){

    }


}

