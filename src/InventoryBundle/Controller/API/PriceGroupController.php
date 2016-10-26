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
            $em = $this->getDoctrine()->getManager();
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
                    'price' => $price,
                    'channels' => $product->getChannels(),
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
    public function updateAllProductPrices(Request $request){
        $changed_products = $request->request->get('changed_products');

        if($changed_products == null)
            return JsonResponse::create(true);

        foreach($changed_products as $product) {
            if($product != "") {
                $em = $this->getDoctrine()->getManager();
                $connection = $em->getConnection();
                $statement = $connection->prepare("select *
	from price_group_prices p
	where product_variant_id = :product_variant_id
	and price_group_id = :price_group_id");
                $statement->bindValue('product_variant_id', $product['product_variant_id']);
                $statement->bindValue('price_group_id', $product['price_group_id']);
                $statement->execute();
                $price_group_price = $statement->fetch();

                //check to see if the entry already exists.
                if($price_group_price == false && $product['price'] != "") {
                    //create it if it doesn't exist
                    $statement = $connection->prepare("insert into price_group_prices (product_variant_id, price_group_id, price) values (:product_variant_id, :price_group_id, :price)");
                    $statement->bindValue('product_variant_id', $product['product_variant_id']);
                    $statement->bindValue('price_group_id', $product['price_group_id']);
                    $statement->bindValue('price', $product['price'] * 100);
                    $statement->execute();
                }
                else {
                    // if price is 0 then delete it
                    // otherwise update value
                    if($product['price'] == 0 || $product['price'] == '') {
                        $statement = $connection->prepare("delete from price_group_prices where id = :id");
                        $statement->bindValue('id', $price_group_price['id']);
                        $statement->execute();
                    }
                    else {
                        $statement = $connection->prepare("update price_group_prices set price = :price where id = :id");
                        $statement->bindValue('price', $product['price'] * 100);
                        $statement->bindValue('id', $price_group_price['id']);
                        $statement->execute();
                    }
                }
            }
        }
        return JsonResponse::create(true);
    }
}

