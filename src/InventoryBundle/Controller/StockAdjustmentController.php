<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\StockAdjustment;
use InventoryBundle\Form\StockAdjustmentType;

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

        $stockAdjustments = $em->getRepository('InventoryBundle:StockAdjustment')->findAll();

        return $this->render('@Inventory/StockAdjustment/index.html.twig', array(
            'stockAdjustments' => $stockAdjustments,
        ));
    }

    /**
     * Creates a new StockAdjustment entity.
     *
     * @Route("/new", name="stockadjustment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $stockAdjustment = new StockAdjustment();
        $form = $this->createForm('InventoryBundle\Form\StockAdjustmentType', $stockAdjustment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stockAdjustment);
            $em->flush();

            return $this->redirectToRoute('stockadjustment_show', array('id' => $stockAdjustment->getId()));
        }

        return $this->render('@Inventory/StockAdjustment/new.html.twig', array(
            'stockAdjustment' => $stockAdjustment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a StockAdjustment entity.
     *
     * @Route("/{id}", name="stockadjustment_show")
     * @Method("GET")
     */
    public function showAction(StockAdjustment $stockAdjustment)
    {
        $deleteForm = $this->createDeleteForm($stockAdjustment);

        return $this->render('@Inventory/StockAdjustment/show.html.twig', array(
            'stockAdjustment' => $stockAdjustment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

//    /**
//     * Displays a form to edit an existing StockAdjustment entity.
//     *
//     * @Route("/{id}/edit", name="stockadjustment_edit")
//     * @Method({"GET", "POST"})
//     */
//    public function editAction(Request $request, StockAdjustment $stockAdjustment)
//    {
//        $deleteForm = $this->createDeleteForm($stockAdjustment);
//        $editForm = $this->createForm('InventoryBundle\Form\StockAdjustmentType', $stockAdjustment);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($stockAdjustment);
//            $em->flush();
//
//            return $this->redirectToRoute('stockadjustment_edit', array('id' => $stockAdjustment->getId()));
//        }
//
//        return $this->render('@Inventory/StockAdjustment/edit.html.twig', array(
//            'stockAdjustment' => $stockAdjustment,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

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
            $em = $this->getDoctrine()->getManager();
            $em->remove($stockAdjustment);
            $em->flush();
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
