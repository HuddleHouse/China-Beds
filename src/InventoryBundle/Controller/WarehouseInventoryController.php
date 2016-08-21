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
     * @Route("/", name="warehouse_inventory_index")d
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->findAll();
        $data = array();

        foreach($warehouses as $warehouse) {
            $quan = $this->getWarehouseInventory($warehouse);

            $data[] = array(
                'id' => $warehouse->getId(),
                'name' => $warehouse->getName(),
                'list_id' => $warehouse->getListId(),
                'quantity' => $quan,
                'po_quantity' => 0
            );
        }

        return $this->render('@Inventory/Warehouse/index.html.twig', array(
            'warehouses' => $data,
        ));
    }


    /**
     * Finds and displays a Warehouse entity.
     *
     * @Route("/{id}", name="warehouse_inventory_shosdfw")
     * @Method("GET")
     */
    public function showAction(Warehouse $warehouse)
    {
        $inventory_data = array();


        $em = $this->getDoctrine()->getManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            foreach($prod->getVariants() as $variant)
                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId()
                );
        }

        return $this->render('@Inventory/WarehouseInventory/show.html.twig', array(
            'warehouse' => $warehouse,
            'inventory_data' => $inventory_data,
            'products' => $products
        ));
    }

    /**
     * Returns the number of total items in the warehouse
     *
     * @param Warehouse $warehouse
     * @return int
     */
    public function getWarehouseInventory(Warehouse $warehouse) {
        if(count($warehouse->getInventory()) == 0)
            return 0;

        $quantity = 0;
        foreach($warehouse->getInventory() as $item) {
            $quantity += $item->getQuantity();
        }
        return $quantity;
    }
    
}
