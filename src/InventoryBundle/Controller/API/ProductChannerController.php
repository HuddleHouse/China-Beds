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
class ProductChannelController extends Controller
{

    /**
     * @Route("/api_get_all_product_variants", name="api_get_all_product_variants")
     */
    public function getVariantsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select * from product_channels i where i.product_id = :id");
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
        $product_id = $request->request->get('product_id');


        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_variant (product_id, name, msrp) values (:product_id, :name, :msrp)");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('name', $name);
        $statement->bindValue('msrp', $msrp);

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
    public function removeProductvariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_variant where :id = id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            return $this->getVariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
