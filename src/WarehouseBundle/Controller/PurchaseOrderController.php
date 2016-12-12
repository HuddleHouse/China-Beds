<?php

namespace WarehouseBundle\Controller;

use WarehouseBundle\Entity\Warehouse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WarehouseBundle\Entity\PurchaseOrder;
use WarehouseBundle\Form\PurchaseOrderType;

/**
 * PurchaseOrder controller.
 *
 * @Route("/purchase-order")
 */
class PurchaseOrderController extends Controller
{
    /**
     * Lists all PurchaseOrder entities.
     *
     * @Route("/", name="purchaseorder_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()->hasRole('ROLE_ADMIN') || $this->getUser()->hasRole('ROLE_SALES_MANAGER') || $this->getUser()->hasRole('ROLE_ACCOUNTING'))
            $purchaseOrders = $em->getRepository('WarehouseBundle:PurchaseOrder')->findByChannel($this->getUser()->getActiveChannel());
        elseif($this->getUser()->hasRole('ROLE_WAREHOUSE')) {
            $id_array = array();
            foreach($this->getUser()->getManagedWarehouses() as $warehouse) {
                if ($warehouse->belongsToChannel($this->getUser()->getActiveChannel()) && $warehouse->isActive()) {
                    $id_array[] = $warehouse->getId();
                }
            }
            $purchaseOrders = $em->getRepository('WarehouseBundle:PurchaseOrder')->findBy(array('warehouse' => $id_array));
        }
        else
            $purchaseOrders = array();


        return $this->render('@Warehouse/PurchaseOrder/index.html.twig', array(
            'purchaseOrders' => $purchaseOrders,
        ));
    }

    /**
     * Creates a new PurchaseOrder entity.
     *
     * @Route("/new", name="purchaseorder_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();

        $channel = $this->getUser()->getActiveChannel();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray(null, $channel);
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

        return $this->render('@Warehouse/PurchaseOrder/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => 'none'
        ));
    }

    /**
     * Finds and displays a PurchaseOrder entity.
     *
     * @Route("/{id}", name="purchaseorder_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(PurchaseOrder $purchaseOrder)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('WarehouseBundle:PurchaseOrder')->getCartArray($purchaseOrder);
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();

        return $this->render('@Warehouse/PurchaseOrder/show.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'cart' => $cart['cart'],
            'total' => $cart['total'],
            'warehouses' => $warehouses,
            'products' => $products
        ));
    }


    /**
     * Deletes a PurchaseOrder entity.
     *
     * @Route("/{id}", name="purchaseorder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PurchaseOrder $purchaseOrder)
    {
        $form = $this->createDeleteForm($purchaseOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($purchaseOrder);
                $em->flush();
                $this->addFlash('notice', 'Purchase Order deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error Deleting Purchase Order: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('purchaseorder_index');
    }

    /**
     * Creates a form to delete a PurchaseOrder entity.
     *
     * @param PurchaseOrder $purchaseOrder The PurchaseOrder entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PurchaseOrder $purchaseOrder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('purchaseorder_delete', array('id' => $purchaseOrder->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
