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
 * @Route("/api/cats")
 */
class CategoriesController extends Controller
{
    /**
     * @Route("/api_add_cat_value", name="api_add_cat_value")
     */
    public function addCatValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $cat_id = $request->request->get('cat_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_categories (category_id, product_id) values (:cat, :product)");
        $statement->bindValue('cat', $cat_id);
        $statement->bindValue('product', $product_id);

        try {
            $statement->execute();
            return $this->getAllCategoriesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_get_all_cat_values", name="api_get_all_cat_values")
     */
    public function getAllCategoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select pc.id, c.name from product_categories pc left join categories c on c.id = pc.category_id where pc.product_id = :id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            $data = $statement->fetchAll();
            return JsonResponse::create($data);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_remove_cat_value", name="api_remove_cat_value")
     */
    public function removeCatValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cat_id = $request->request->get('cat_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_categories where :cat_id = id");
        $statement->bindValue('cat_id', $cat_id);

        try {
            $statement->execute();
            return $this->getAllCategoriesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
