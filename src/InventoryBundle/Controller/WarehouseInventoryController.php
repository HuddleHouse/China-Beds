<?php

namespace InventoryBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Warehouse;
use InventoryBundle\Form\WarehouseType;
use QuickbooksBundle\Controller\ItemController;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse/inventory")
 */
class WarehouseInventoryController extends Controller
{
    /**
     * Lists all Warehouse entities.
     *
     * @Route("/", name="warehouse_inventory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->findAll();

        $data = array();
        $itemController = new ItemController();

        foreach($warehouses as $warehouse) {
            $quan = $itemController->qbQuantityForWarehouse($warehouse->getListId());
            $data[] = array(
                'id' => $warehouse->getId(),
                'name' => $warehouse->getName(),
                'list_id' => $warehouse->getListId(),
                'quantity' => $quan->quantity,
                'po_quantity' => $quan->po_quantity
            );
        }

        return $this->render('@Inventory/WarehouseInventory/index.html.twig', array(
            'warehouses' => $data,
        ));
    }


    /**
     * Finds and displays a Warehouse entity.
     *
     * @Route("/{id}", name="warehouse_inventory_show")
     * @Method("GET")
     */
    public function showAction(Warehouse $warehouse)
    {
        $itemController = new ItemController();
        $inventory_data = $itemController->qbItemQuantityForWarehouse($warehouse->getListId());

        return $this->render('@Inventory/WarehouseInventory/show.html.twig', array(
            'warehouse' => $warehouse,
            'inventory_data' => $inventory_data
        ));
    }
    
}
