<?php

namespace WarehouseBundle\Controller;

use InventoryBundle\Entity\Channel;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WarehouseBundle\Entity\Warehouse;
use WarehouseBundle\Form\WarehouseType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse")
 */
class WarehouseController extends Controller
{
    /**
     * Lists all Warehouse entities.
     *
     * @Route("/", name="warehouse_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        return $this->render('@Warehouse/Warehouse/index.html.twig', array(
            'warehouses' => $warehouses,
        ));
    }

    /**
     * Shows Warehouses in a Channel
     *
     * @Route("/channel/{channelName}", name="warehouse_channelled")
     */
    public function channelledIndexAction($channelName)
    {
        $em = $this->getDoctrine()->getManager();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesInChannelArray($channelName);

        return $this->render('@Warehouse/Warehouse/index.html.twig', array(
            'warehouses' => $warehouses,
        ));
    }

    /**
     * Creates a new Warehouse entity.
     *
     * @Route("/new", name="warehouse_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $warehouse = new Warehouse();
        $form = $this->createForm('WarehouseBundle\Form\WarehouseType', $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($warehouse);
                $em->flush();
                $this->addFlash('notice', 'Warehouse created successfully.');
                return $this->redirectToRoute('warehouse_edit', array('id' => $warehouse->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating warehouse ' . $e->getMessage());

                return $this->render('@Warehouse/Warehouse/new.html.twig', array(
                    'warehouse' => $warehouse,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Warehouse/Warehouse/new.html.twig', array(
            'warehouse' => $warehouse,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Warehouse entity.
     *
     * @Route("/{id}", name="warehouse_show")
     * @Method("GET")
     */
    public function showAction(\WarehouseBundle\Entity\Warehouse $warehouse)
    {
        $deleteForm = $this->createDeleteForm($warehouse);

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsArray();
        $inventory_data = $em->getRepository('WarehouseBundle:WareHouse')->getWarehouseInventoryArray($warehouse);
        $active_po = $em->getRepository('WarehouseBundle:PurchaseOrder')->getActiveForWarehouseArray($warehouse);
        $all_po = $em->getRepository('WarehouseBundle:PurchaseOrder')->getAllForWarehouseArray($warehouse);

        $all_st = $em->getRepository('WarehouseBundle:StockTransfer')->getAllForWarehouseArray($warehouse);
        $active_st = $em->getRepository('WarehouseBundle:StockTransfer')->getActiveForWarehouseArray($warehouse);

        $all_adj = $em->getRepository('WarehouseBundle:StockAdjustment')->getAllForWarehouseArray($warehouse);
        $active_adj = $em->getRepository('WarehouseBundle:StockAdjustment')->getActiveForWarehouseArray($warehouse);

        $all = array_merge($all_po, $all_st, $all_adj);
        $active = array_merge($active_po, $active_st, $active_adj);

        return $this->render('@Warehouse/Warehouse/show.html.twig', array(
            'warehouse' => $warehouse,
            'delete_form' => $deleteForm->createView(),
            'products' => $products,
            'inventory_data' => $inventory_data,
            'active_po' => $active_po,
            'all_po' => $all_po,
            'all_st' => $all_st,
            'all_adj' => $all_adj,
            'all' => $all,
            'active' => $active
        ));
    }

    /**
     * Displays a form to edit an existing Warehouse entity.
     *
     * @Route("/{id}/edit", name="warehouse_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Warehouse $warehouse)
    {
        $deleteForm = $this->createDeleteForm($warehouse);
        $editForm = $this->createForm('WarehouseBundle\Form\WarehouseType', $warehouse);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($warehouse);
                $em->flush();

                $this->addFlash('notice', 'Warehouse updated successfully.');
                return $this->render('@Warehouse/Warehouse/edit.html.twig', array(
                    'warehouse' => $warehouse,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
            catch(\Exception $e) {
                //$this->addFlash('error', 'Error updating warehouse ' . $e->getMessage());
                $this->addFlash('error', 'Error deleting warehouse, the warehouse selected is being used currently for another purpose and deleting it would cause further issues' );
                
                return $this->render('@Warehouse/Warehouse/edit.html.twig', array(
                    'warehouse' => $warehouse,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Warehouse/Warehouse/edit.html.twig', array(
            'warehouse' => $warehouse,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Warehouse entity.
     *
     * @Route("/{id}", name="warehouse_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Warehouse $warehouse)
    {
        $form = $this->createDeleteForm($warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($warehouse);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting warehouse ' . $e->getMessage());

                return $this->redirectToRoute('warehouse_index');
            }
        }

        return $this->redirectToRoute('warehouse_index');
    }

    /**
     * Creates a form to delete a Warehouse entity.
     *
     * @param Warehouse $warehouse The Warehouse entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Warehouse $warehouse)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('warehouse_delete', array('id' => $warehouse->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     *
     * @Route("/{id}/purchase-order", name="warehouse_new_purchase_order")
     */
    public function warehouseNewPurchaseOrderAction(Warehouse $warehouse)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray($warehouse);
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        return $this->render('@Warehouse/PurchaseOrder/new.html.twig', array(
            'warehouse' => $warehouse,
            'inventory_data' => $inventory_data,
            'warehouses' => $warehouses,
            'warehouse_id' => $warehouse->getId(),
            'products' => $products
        ));
    }

    /**
     *
     * @Route("/{id}/stock-transfer", name="warehouse_new_stock_transfer")
     */
    public function warehouseNewStockTransferAction(Warehouse $warehouse)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        return $this->render('@Warehouse/StockTransfer/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $warehouse->getId()
        ));
    }

    /**
     *
     * @Route("/{id}/stock-adjustment", name="warehouse_new_stock_adjustment")
     */
    public function newStockAdjustmentAction(Warehouse $warehouse)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray();

        return $this->render('@Warehouse/StockAdjustment/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $warehouse->getId()
        ));
    }


    /**
     *
     * @Route("/{id}/inventory", name="warehouse_inventory_show")
     */
    public function warehouseInventoryShowAction(Warehouse $warehouse)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsArray();

        return $this->render('@Warehouse/Warehouse/inventory.html.twig', array(
            'warehouse' => $warehouse,
            'inventory_data' => $inventory_data,
            'products' => $products
        ));
    }


}
