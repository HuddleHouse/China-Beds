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
class OptionsController extends Controller
{
    /**
     * @Route("/add-option", name="api_add_option_value")
     */
    public function addOptionValueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $new_option_value = $request->request->get('new_option_value');

        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into option_values (value, option_id) values (:val, :option_id)");
        $statement->bindValue('val', $new_option_value);
        $statement->bindValue('option_id', $id);

        try {
            $statement->execute();
            return $this->getValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_get_values", name="api_get_values")
     */
    public function getValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select value, option_id, id from option_values where option_id = :option_id");
        $statement->bindValue('option_id', $id);


        try {
            $statement->execute();
            $data = $statement->fetchAll();
            return JsonResponse::create($data);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
