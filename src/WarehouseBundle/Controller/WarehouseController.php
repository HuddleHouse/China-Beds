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
     * @Route("/", name="warehouse_channelled")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if(!$user->hasRole('ROLE_WAREHOUSE'))
            $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->findBy(['channel' => $user->getActiveChannel()]);
        else {
            foreach($user->getManagedWarehouses() as $warehouse) {
                if ( $warehouse->getChannel()->getId() == $user->getActiveChannel()->getId() ) {
                    $warehouses[] = $warehouse;
                }
            }
        }

        $data = [];
        foreach($warehouses as $warehouse) {
            $data[] = array(
                'id' => $warehouse->getId(),
                'name' => $warehouse->getName(),
                'list_id' => $warehouse->getListId(),
                'quantity' => $em->getRepository('WarehouseBundle:Warehouse')->getWarehouseInventory($warehouse),
                'po_quantity' => $em->getRepository('WarehouseBundle:Warehouse')->getWarehouseInventoryOnPurchaseOrder($warehouse),
                'active' => $warehouse->isActive()
            );
        }

        return $this->render('@Warehouse/Warehouse/index.html.twig', array(
            'warehouses' => $data,
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
                if ($warehouse->getChannels()->isEmpty()) {
                    $channel = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());
                    $warehouse->addChannel($channel);
                }
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
        $pop = $em->getRepository('InventoryBundle:PopItem')->findAll();
        $inventory_data = $em->getRepository('WarehouseBundle:WareHouse')->getWarehouseInventoryArray($warehouse);
        $pop_inventory_data = $em->getRepository('WarehouseBundle:WareHouse')->getWarehousePopInventoryArray($warehouse);
        $active_po = $em->getRepository('WarehouseBundle:PurchaseOrder')->getActiveForWarehouseArray($warehouse);
        $all_po = $em->getRepository('WarehouseBundle:PurchaseOrder')->getAllForWarehouseArray($warehouse);

        $all_st = $em->getRepository('WarehouseBundle:StockTransfer')->getAllForWarehouseArray($warehouse);
        $active_st = $em->getRepository('WarehouseBundle:StockTransfer')->getActiveForWarehouseArray($warehouse);

        $all_adj = $em->getRepository('WarehouseBundle:StockAdjustment')->getAllForWarehouseArray($warehouse);
        $active_adj = $em->getRepository('WarehouseBundle:StockAdjustment')->getActiveForWarehouseArray($warehouse);

        $all_orders = $em->getRepository('OrderBundle:Orders')->getAllForWarehouseArray($warehouse);
        $active_orders = $em->getRepository('OrderBundle:Orders')->getActiveForWarehouseArray($warehouse);

        $all = array_merge($all_po, $all_st, $all_adj, $all_orders);
        $all_dates = array();
        foreach($all as $item) {
            if(isset($item['date']))
                $all_dates[] = $item['date'];
            else if(isset($item['stock_due_date']))
                $all_dates[] = $item['stock_due_date'];

        }

        array_multisort($all_dates, SORT_DESC, $all);

        $active = array_merge($active_po, $active_st, $active_adj, $active_orders);
        $active_dates = array();
        foreach($active as $item) {
            if(isset($item['date']))
                $active_dates[] = $item['date'];
            else if(isset($item['stock_due_date']))
                $active_dates[] = $item['stock_due_date'];

        }

        array_multisort($active_dates, SORT_DESC, $active);

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
            'active' => $active,
            'pop_inventory' => $pop_inventory_data,
            'pop' => $pop,
            'orders' => $all_orders
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
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

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
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

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
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

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
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsArray($this->getUser());

        return $this->render('@Warehouse/Warehouse/inventory.html.twig', array(
            'warehouse' => $warehouse,
            'inventory_data' => $inventory_data,
            'products' => $products
        ));
    }


}
