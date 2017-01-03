<?php

namespace WarehouseBundle\Controller\API;

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
     * @Route("/api_get_warehouse_products", name="api_get_warehouse_products")
     */
    public function getProductsForWarehouse(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $warehouse_id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

        return new JsonResponse($em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray($warehouse));
    }

    /**
     * @Route("/api_add_warehouse_inventory", name="api_add_warehouse_inventory")
     */
    public function addWarehouseInventoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $variant_id = $request->request->get('variant_id');
        $quantity = (int)$request->request->get('quantity');
        $warehouse_id = $request->request->get('warehouse_id');

        try {
            $variant = $em->getRepository('InventoryBundle:ProductVariant')->find($variant_id);
            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);

            $this->get('warehouse.warehouse_service')->modifyInventoryLevel($warehouse, $variant, $quantity);

            return $this->getValuesAction($request);
        } catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_add_warehouse_pop_inventory", name="api_add_warehouse_pop_inventory")
     */
    public function addWarehousePopInventoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pop_id = $request->request->get('pop_id');
        $quantity = (int)$request->request->get('quantity');
        $warehouse_id = $request->request->get('warehouse_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select quantity from warehouse_pop_inventory where pop_item_id = :pop_item_id and warehouse_id = :warehouse_id");
        $statement->bindValue('pop_item_id', $pop_id);
        $statement->bindValue('warehouse_id', $warehouse_id);

        try {
            $statement->execute();
            $price = $statement->fetch();

            if($price == false) {
                $statement = $connection->prepare("insert into warehouse_pop_inventory (quantity, pop_item_id, warehouse_id) values (:quantity, :pop_item_id, :warehouse_id)");
                $statement->bindValue('quantity', $quantity);
                $statement->bindValue('pop_item_id', $pop_id);
                $statement->bindValue('warehouse_id', $warehouse_id);
                $statement->execute();
            }
            else {
                $quantity = (int)$quantity + $price['quantity'];
                $statement = $connection->prepare("update warehouse_pop_inventory set quantity = :quantity where pop_item_id = :pop_item_id and warehouse_id = :warehouse_id");
                $statement->bindValue('quantity', $quantity);
                $statement->bindValue('pop_item_id', $pop_id);
                $statement->bindValue('warehouse_id', $warehouse_id);
                $statement->execute();
            }

            return $this->getPopValuesAction($request);
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
        $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($id);
        $inventory_data = $em->getRepository('WarehouseBundle:WareHouse')->getWarehouseInventoryArray($warehouse);

        if($inventory_data === true)
            return JsonResponse::create(true);
        return JsonResponse::create(array('inventory' => $inventory_data));
    }

    public function getPopValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('warehouse_id');
        $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($id);
        $pop_inventory_data = $em->getRepository('WarehouseBundle:WareHouse')->getWarehousePopInventoryArray($warehouse);

        if($pop_inventory_data === true)
            return JsonResponse::create(true);
        return JsonResponse::create(array('pop_inventory' => $pop_inventory_data));
    }
}
