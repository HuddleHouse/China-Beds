<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Rebate;
use InventoryBundle\Form\RebateType;

/**
 * Rebate controller.
 *
 * @Route("/rebate")
 */
class RebateController extends Controller
{
    /**
     * Lists all Rebate entities.
     *
     * @Route("/", name="rebate_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rebates = $em->getRepository('InventoryBundle:Rebate')->findAll();

        return $this->render('@Inventory/Rebate/index.html.twig', array(
            'rebates' => $rebates,
        ));
    }

    /**
     * Creates a new Rebate entity.
     *
     * @Route("/new", name="rebate_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $rebate = new Rebate();
        $form = $this->createForm('InventoryBundle\Form\RebateType', $rebate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($rebate);
                $em->flush();
                $this->addFlash('notice', 'Rebate Created successfully.');
                return $this->redirectToRoute('rebate_show', array('id' => $rebate->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Rebate ' . $e->getMessage());
                return $this->render('@Inventory/Rebate/new.html.twig', array(
                    'rebate' => $rebate,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('@Inventory/Rebate/new.html.twig', array(
            'rebate' => $rebate,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Rebate entity.
     *
     * @Route("/{id}", name="rebate_show")
     * @Method("GET")
     */
    public function showAction(Rebate $rebate)
    {
        $deleteForm = $this->createDeleteForm($rebate);

        return $this->render('@Inventory/Rebate/show.html.twig', array(
            'rebate' => $rebate,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Rebate entity.
     *
     * @Route("/{id}/edit", name="rebate_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Rebate $rebate)
    {
        $deleteForm = $this->createDeleteForm($rebate);
        $editForm = $this->createForm('InventoryBundle\Form\RebateType', $rebate);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($rebate);
                $em->flush();
                $this->addFlash('notice', 'Rebate updated successfully.');

                return $this->redirectToRoute('rebate_edit', array('id' => $rebate->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Rebate ' . $e->getMessage());

                return $this->render('@Inventory/Rebate/edit.html.twig', array(
                    'rebate' => $rebate,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Rebate/edit.html.twig', array(
            'rebate' => $rebate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Rebate entity.
     *
     * @Route("/{id}", name="rebate_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Rebate $rebate)
    {
        $form = $this->createDeleteForm($rebate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($rebate);
                $em->flush();
                $this->addFlash('notice', 'Rebate Deleted successfully.');

            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Rebate ' . $e->getMessage());

                return $this->redirectToRoute('rebate_index');
            }
        }

        return $this->redirectToRoute('rebate_index');
    }

    /**
     * Creates a form to delete a Rebate entity.
     *
     * @param Rebate $rebate The Rebate entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rebate $rebate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rebate_delete', array('id' => $rebate->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
