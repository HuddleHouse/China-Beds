<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\WarrantyClaim;
use InventoryBundle\Form\WarrantyClaimType;

/**
 * WarrantyClaim controller.
 *
 * @Route("/warrantyclaim")
 */
class WarrantyClaimController extends Controller
{
    /**
     * Lists all WarrantyClaim entities.
     *
     * @Route("/", name="warrantyclaim_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $warrantyClaims = $em->getRepository('InventoryBundle:WarrantyClaim')->findAll();

        return $this->render('warrantyclaim/index.html.twig', array(
            'warrantyClaims' => $warrantyClaims,
        ));
    }

    /**
     * Creates a new WarrantyClaim entity.
     *
     * @Route("/new", name="warrantyclaim_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $warrantyClaim = new WarrantyClaim();
        $form = $this->createForm('InventoryBundle\Form\WarrantyClaimType', $warrantyClaim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($warrantyClaim);
            $em->flush();

            return $this->redirectToRoute('warrantyclaim_show', array('id' => $warrantyClaim->getId()));
        }

        return $this->render('warrantyclaim/new.html.twig', array(
            'warrantyClaim' => $warrantyClaim,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a WarrantyClaim entity.
     *
     * @Route("/{id}", name="warrantyclaim_show")
     * @Method("GET")
     */
    public function showAction(WarrantyClaim $warrantyClaim)
    {
        $deleteForm = $this->createDeleteForm($warrantyClaim);

        return $this->render('warrantyclaim/show.html.twig', array(
            'warrantyClaim' => $warrantyClaim,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WarrantyClaim entity.
     *
     * @Route("/{id}/edit", name="warrantyclaim_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, WarrantyClaim $warrantyClaim)
    {
        $deleteForm = $this->createDeleteForm($warrantyClaim);
        $editForm = $this->createForm('InventoryBundle\Form\WarrantyClaimType', $warrantyClaim);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($warrantyClaim);
            $em->flush();

            return $this->redirectToRoute('warrantyclaim_edit', array('id' => $warrantyClaim->getId()));
        }

        return $this->render('warrantyclaim/edit.html.twig', array(
            'warrantyClaim' => $warrantyClaim,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WarrantyClaim entity.
     *
     * @Route("/{id}", name="warrantyclaim_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, WarrantyClaim $warrantyClaim)
    {
        $form = $this->createDeleteForm($warrantyClaim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($warrantyClaim);
            $em->flush();
        }

        return $this->redirectToRoute('warrantyclaim_index');
    }

    /**
     * Creates a form to delete a WarrantyClaim entity.
     *
     * @param WarrantyClaim $warrantyClaim The WarrantyClaim entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WarrantyClaim $warrantyClaim)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('warrantyclaim_delete', array('id' => $warrantyClaim->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
