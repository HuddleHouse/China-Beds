<?php

namespace WarehouseBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WarehouseBundle\Entity\StockAdjustment;
use WarehouseBundle\Form\StockAdjustmentType;

/**
 * StockAdjustment controller.
 *
 * @Route("/adjustment")
 */
class StockAdjustmentController extends Controller
{
    /**
     * Lists all StockAdjustment entities.
     *
     * @Route("/", name="stockadjustment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stockAdjustments = $em->getRepository('WarehouseBundle:StockAdjustment')->findBy([], ['date' => 'DESC']);

        return $this->render('@Warehouse/StockAdjustment/index.html.twig', array(
            'stockAdjustments' => $stockAdjustments,
        ));
    }

    /**
     * Creates a new StockAdjustment entity.
     *
     * @Route("/new", name="stockadjustment_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());

        return $this->render('@Warehouse/StockAdjustment/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => 'none'
        ));
    }

    /**
     * Finds and displays a StockAdjustment entity.
     *
     * @Route("/{id}", name="stockadjustment_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(StockAdjustment $stockAdjustment)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getAllWarehousesArray($this->getUser()->getActiveChannel());
        $cart = $em->getRepository('WarehouseBundle:StockAdjustment')->getCartArray($stockAdjustment);

        return $this->render('@Warehouse/StockAdjustment/show.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'stockAdjustment' => $stockAdjustment,
            'cart' => $cart
        ));
    }

    /**
     * Deletes a StockAdjustment entity.
     *
     * @Route("/{id}", name="stockadjustment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, StockAdjustment $stockAdjustment)
    {
        $form = $this->createDeleteForm($stockAdjustment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($stockAdjustment);
                $em->flush();
                $this->addFlash('notice', 'Stock Adjustment deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Stock Adjustment: ' . $e->getMessage());

                return $this->redirectToRoute('stockadjustment_index');
            }

        }

        return $this->redirectToRoute('stockadjustment_index');
    }

    /**
     * Creates a form to delete a StockAdjustment entity.
     *
     * @param StockAdjustment $stockAdjustment The StockAdjustment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StockAdjustment $stockAdjustment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stockadjustment_delete', array('id' => $stockAdjustment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
