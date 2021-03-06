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
 * @Route("/api/specs")
 */
class SpecificationsController extends Controller
{
    /**
     * @Route("/api_add_spec_value", name="api_add_spec_value")
     */
    public function addSpecValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->request->get('product_id');
        $spec_id = $request->request->get('spec_id');
        $spec_value = $request->request->get('spec_value');

        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_specification (specification_id, product_id, description) values (:spec, :product, :description)");
        $statement->bindValue('spec', $spec_id);
        $statement->bindValue('product', $product_id);
        $statement->bindValue('description', $spec_value);

        try {
            $statement->execute();
            return $this->getAllSpecValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_get_all_spec_values", name="api_get_all_spec_values")
     */
    public function getAllSpecValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select s.id, s.description, sp.name from product_specification s left join specifications sp on sp.id = s.specification_id where s.product_id = :id");
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
     * @Route("/api_remove_spec_value", name="api_remove_spec_value")
     */
    public function removeSpecValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $spec_id = $request->request->get('spec_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_specification where :spec_id = id");
        $statement->bindValue('spec_id', $spec_id);

        try {
            $statement->execute();
            return $this->getAllSpecValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_update_spec_value", name="api_update_spec_value")
     */
    public function updateSpecValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $description = $request->request->get('description');

        $connection = $em->getConnection();
        $statement = $connection->prepare("update product_specification set description = :description where id = :id");
        $statement->bindValue('description', $description);
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            return $this->getAllSpecValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
