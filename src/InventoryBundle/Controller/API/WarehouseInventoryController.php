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
class WarehouseInventoryController extends Controller
{
    /**
     * @Route("/api_add_warehouse_inventory", name="api_add_warehouse_inventory")
     */
    public function addWarehouseInventoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $variant_id = $request->request->get('variant_id');
        $quantity = (int)$request->request->get('quantity');
        $warehouse_id = $request->request->get('warehouse_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select quantity from warehouse_inventory where product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
        $statement->bindValue('product_variant_id', $variant_id);
        $statement->bindValue('warehouse_id', $warehouse_id);

        try {
            $statement->execute();
            $price = $statement->fetch();

            if($price == false) {
                $statement = $connection->prepare("insert into warehouse_inventory (quantity, product_variant_id, warehouse_id) values (:quantity, :product_variant_id, :warehouse_id)");
                $statement->bindValue('quantity', $quantity);
                $statement->bindValue('product_variant_id', $variant_id);
                $statement->bindValue('warehouse_id', $warehouse_id);
                $statement->execute();
            }
            else {
                $quantity = (int)$quantity + $price['quantity'];
                $statement = $connection->prepare("update warehouse_inventory set quantity = :quantity where product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
                $statement->bindValue('quantity', $quantity);
                $statement->bindValue('product_variant_id', $variant_id);
                $statement->bindValue('warehouse_id', $warehouse_id);
                $statement->execute();
            }

            return $this->getValuesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_get_warehouse_inventory", name="api_get_warehouse_inventory")
     */
    public function getValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('InventoryBundle:Warehouse')->find($id);
        $inventory_data = $em->getRepository('InventoryBundle:WareHouse')->getWarehouseInventoryArray($warehouse);

        if($inventory_data === true)
            return JsonResponse::create(true);
        return JsonResponse::create($inventory_data);
    }
}
