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
class ProductVariantController extends Controller
{

    /**
     * @Route("/api_get_all_product_variants", name="api_get_all_product_variants")
     */
    public function getVariantsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select *, round(i.msrp/100, 2) as msrp from product_variant i where i.product_id = :id");
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
     * @Route("/add-product-variant", name="api_add_product_variant")
     */
    public function addProductVariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $msrp = $request->request->get('msrp');
        $msrp = $msrp*100;
        $sku = $request->request->get('sku');
        $product_id = $request->request->get('product_id');
        $weight = $request->request->get('weight');
        $fedex_dimensions = $request->request->get('fedex_dimensions');


        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_variant (product_id, name, msrp, sku, weight, fedex_dimensions) values (:product_id, :name, :msrp, :sku, :weight, :fedex_dimensions)");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('name', $name);
        $statement->bindValue('msrp', $msrp);
        $statement->bindValue('sku', $sku);
        $statement->bindValue('weight', $weight);
        $statement->bindValue('fedex_dimensions', $fedex_dimensions);

        try {
            $statement->execute();
            return $this->getVariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_remove_product_variant", name="api_remove_product_variant")
     */
    public function removeProductVariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_variant where id = :id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            return $this->getVariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_update_product_variant", name="api_update_product_variant")
     */
    public function updateProductVariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $msrp = $request->request->get('msrp');
        $msrp = $msrp*100;
        $sku = $request->request->get('sku');
        $weight = $request->request->get('weight');
        $fedex_dimensions = $request->request->get('fedex_dimensions');

        $connection = $em->getConnection();
        $statement = $connection->prepare("update product_variant set name = :name, msrp = :msrp, sku = :sku, weight = :weight, fedex_dimensions = :fedex_dimensions where id = :id");

        $statement->bindValue('id', $id);
        $statement->bindValue('name', $name);
        $statement->bindValue('msrp', $msrp);
        $statement->bindValue('sku', $sku);
        $statement->bindValue('weight', $weight);
        $statement->bindValue('fedex_dimensions', $fedex_dimensions);

        try {
            $statement->execute();
            return $this->getVariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
