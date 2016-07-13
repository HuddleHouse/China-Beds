<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\RebateSubmission;
use InventoryBundle\Form\RebateSubmissionType;

/**
 * RebateSubmission controller.
 *
 * @Route("/rebate/submit")
 */
class RebateSubmissionController extends Controller
{
    /**
     * Lists all RebateSubmission entities.
     *
     * @Route("/", name="rebate_submit_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rebateSubmissions = $em->getRepository('InventoryBundle:RebateSubmission')->findAll();

        return $this->render('@Inventory/RebateSubmission/index.html.twig', array(
            'rebateSubmissions' => $rebateSubmissions,
        ));
    }

    /**
     * Creates a new RebateSubmission entity.
     *
     * @Route("/new", name="rebate_submit_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $rebateSubmission = new RebateSubmission();
        $form = $this->createForm('InventoryBundle\Form\RebateSubmissionType', $rebateSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rebateSubmission);
            $em->flush();

            return $this->redirectToRoute('rebate_submit_show', array('id' => $rebateSubmission->getId()));
        }

        return $this->render('@Inventory/RebateSubmission/new.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RebateSubmission entity.
     *
     * @Route("/{id}", name="rebate_submit_show")
     * @Method("GET")
     */
    public function showAction(RebateSubmission $rebateSubmission)
    {
        $deleteForm = $this->createDeleteForm($rebateSubmission);

        return $this->render('@Inventory/RebateSubmission/show.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RebateSubmission entity.
     *
     * @Route("/{id}/edit", name="rebate_submit_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RebateSubmission $rebateSubmission)
    {
        $deleteForm = $this->createDeleteForm($rebateSubmission);
        $editForm = $this->createForm('InventoryBundle\Form\RebateSubmissionType', $rebateSubmission);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rebateSubmission);
            $em->flush();

            return $this->redirectToRoute('rebate_submit_edit', array('id' => $rebateSubmission->getId()));
        }

        return $this->render('@Inventory/RebateSubmission/edit.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a RebateSubmission entity.
     *
     * @Route("/{id}", name="rebate_submit_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, RebateSubmission $rebateSubmission)
    {
        $form = $this->createDeleteForm($rebateSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rebateSubmission);
            $em->flush();
        }

        return $this->redirectToRoute('rebate_submit_index');
    }

    /**
     * Creates a form to delete a RebateSubmission entity.
     *
     * @param RebateSubmission $rebateSubmission The RebateSubmission entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RebateSubmission $rebateSubmission)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rebate_submit_delete', array('id' => $rebateSubmission->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
