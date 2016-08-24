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
 * @Route("/transfer")
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
     * @Route("/new", name="stocktransfer_new")
     * @Method({"GET", "POST"})
     */
    public function newAction()
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->findAll();

        return $this->render('@Inventory/StockTransfer/new.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses
        ));
    }

    /**
     * Finds and displays a StockTransfer entity.
     *
     * @Route("/{id}", name="stocktransfer_show")
     * @Method("GET")
     */
    public function showAction(StockTransfer $stockTransfer)
    {
        $inventory_data = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('InventoryBundle:Product')->getAllProductsWithQuantityArray();
        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->findAll();
        $cart = $em->getRepository('InventoryBundle:StockTransfer')->getCartArray($stockTransfer);

        return $this->render('@Inventory/StockTransfer/show.html.twig', array(
            'inventory_data' => $inventory_data,
            'products' => $products,
            'warehouses' => $warehouses,
            'stockTransfer' => $stockTransfer,
            'cart' => $cart
        ));
    }

//    /**
//     * Displays a form to edit an existing StockTransfer entity.
//     *
//     * @Route("/{id}/edit", name="stocktransfer_edit")
//     * @Method({"GET", "POST"})
//     */
//    public function editAction(Request $request, StockTransfer $stockTransfer)
//    {
//        $deleteForm = $this->createDeleteForm($stockTransfer);
//        $editForm = $this->createForm('InventoryBundle\Form\StockTransferType', $stockTransfer);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($stockTransfer);
//            $em->flush();
//
//            return $this->redirectToRoute('stocktransfer_edit', array('id' => $stockTransfer->getId()));
//        }
//
//        return $this->render('@Inventory/StockTransfer/edit.html.twig', array(
//            'stockTransfer' => $stockTransfer,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

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
