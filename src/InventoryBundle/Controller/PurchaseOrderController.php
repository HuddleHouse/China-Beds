<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\PurchaseOrder;
use InventoryBundle\Form\PurchaseOrderType;

/**
 * PurchaseOrder controller.
 *
 * @Route("/purchaseorder")
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

        $purchaseOrders = $em->getRepository('InventoryBundle:PurchaseOrder')->findAll();

        return $this->render('@Inventory/PurchaseOrder/index.html.twig', array(
            'purchaseOrders' => $purchaseOrders,
        ));
    }

    /**
     * Creates a new PurchaseOrder entity.
     *
     * @Route("/new", name="purchaseorder_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $purchaseOrder = new PurchaseOrder();
        $form = $this->createForm('InventoryBundle\Form\PurchaseOrderType', $purchaseOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($purchaseOrder);
            $em->flush();

            return $this->redirectToRoute('purchaseorder_show', array('id' => $purchaseOrder->getId()));
        }

        return $this->render('@Inventory/PurchaseOrder/new.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseOrder entity.
     *
     * @Route("/{id}", name="purchaseorder_show")
     * @Method("GET")
     */
    public function showAction(PurchaseOrder $purchaseOrder)
    {
        $deleteForm = $this->createDeleteForm($purchaseOrder);

        return $this->render('@Inventory/PurchaseOrder/show.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseOrder entity.
     *
     * @Route("/{id}/edit", name="purchaseorder_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PurchaseOrder $purchaseOrder)
    {
        $deleteForm = $this->createDeleteForm($purchaseOrder);
        $editForm = $this->createForm('InventoryBundle\Form\PurchaseOrderType', $purchaseOrder);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($purchaseOrder);
            $em->flush();

            return $this->redirectToRoute('purchaseorder_edit', array('id' => $purchaseOrder->getId()));
        }

        return $this->render('@Inventory/PurchaseOrder/edit.html.twig', array(
            'purchaseOrder' => $purchaseOrder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
            $em = $this->getDoctrine()->getManager();
            $em->remove($purchaseOrder);
            $em->flush();
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
