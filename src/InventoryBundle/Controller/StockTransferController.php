<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\StockTransfer;
use InventoryBundle\Form\StockTransferType;

/**
 * StockTransfer controller.
 *
 * @Route("/stock-transfer")
 */
class StockTransferController extends Controller
{
    /**
     * Lists all StockTransfer entities.
     *
     * @Route("/", name="stocktransfer_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stockTransfers = $em->getRepository('InventoryBundle:StockTransfer')->findAll();

        return $this->render('@Inventory/StockTransfer/index.html.twig', array(
            'stockTransfers' => $stockTransfers,
        ));
    }

    /**
     * Creates a new StockTransfer entity.
     *
     * @Route("/new", name="stocktransfer_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction()
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->getAllWarehousesArray();

        return $this->render('@Inventory/StockTransfer/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => 'none'
        ));
    }

    /**
     * Finds and displays a StockTransfer entity.
     *
     * @Route("/{id}", name="stocktransfer_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(StockTransfer $stockTransfer)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->getAllWarehousesArray();
        $cart = $em->getRepository('InventoryBundle:StockTransfer')->getCartArray($stockTransfer);

        return $this->render('@Inventory/StockTransfer/show.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'stockTransfer' => $stockTransfer,
            'cart' => $cart
        ));
    }

    /**
     * Deletes a StockTransfer entity.
     *
     * @Route("/{id}", name="stocktransfer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, StockTransfer $stockTransfer)
    {
        $form = $this->createDeleteForm($stockTransfer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($stockTransfer);
                $em->flush();
                $this->addFlash('notice', 'Stock Transfer deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Stock Transfer: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('vstocktransfer_index');
    }

    /**
     * Creates a form to delete a StockTransfer entity.
     *
     * @param StockTransfer $stockTransfer The StockTransfer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StockTransfer $stockTransfer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stocktransfer_delete', array('id' => $stockTransfer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
