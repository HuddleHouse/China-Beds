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
 * @Route("/api/attributes")
 */
class AttributesController extends Controller
{
    /**
     * @Route("/add-attribute-value", name="api_add_attribute_value")
     */
    public function addAttributeValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $attribute_id = $request->request->get('attribute_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_attributes (attribute_id, product_id) values (:spec, :product)");
        $statement->bindValue('spec', $attribute_id);
        $statement->bindValue('product', $product_id);

        try {
            $statement->execute();
            return $this->getAllAttributeValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_get_all_attribute_values", name="api_get_all_attribute_values")
     */
    public function getAllAttributeValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select s.id, sp.name, sp.path, sp.alt_tag from product_attributes s left join attribute sp on sp.id = s.attribute_id where s.product_id = :id");
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
     * @Route("/api_remove_attribute_value", name="api_remove_attribute_value")
     */
    public function removeAttributeValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $attribute_id = $request->request->get('attribute_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_attributes where :attribute_id = id");
        $statement->bindValue('attribute_id', $attribute_id);

        try {
            $statement->execute();
            return $this->getAllAttributeValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
