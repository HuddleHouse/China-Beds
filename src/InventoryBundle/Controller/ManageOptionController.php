<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\ManageOption;
use InventoryBundle\Form\ManageOptionType;

/**
 * ManageOption controller.
 *
 * @Route("/manage-options")
 */
class ManageOptionController extends Controller
{
    /**
     * Lists all ManageOption entities.
     *
     * @Route("/", name="manage_options_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $manageOptions = $em->getRepository('InventoryBundle:ManageOption')->findAll();

        return $this->render('@Inventory/ManageOption/index.html.twig', array(
            'manageOptions' => $manageOptions,
        ));
    }

    /**
     * Creates a new ManageOption entity.
     *
     * @Route("/new", name="manage_options_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $manageOption = new ManageOption();
        $form = $this->createForm('InventoryBundle\Form\ManageOptionType', $manageOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($manageOption);
                $em->flush();
                $this->addFlash('notice', 'Option created successfully.');
                return $this->redirectToRoute('manage_options_edit', array('id' => $manageOption->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Options: ' . $e->getMessage());

                return $this->render('@Inventory/ManageOption/new.html.twig', array(
                    'manageOption' => $manageOption,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('@Inventory/ManageOption/new.html.twig', array(
            'manageOption' => $manageOption,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ManageOption entity.
     *
     * @Route("/{id}", name="manage_options_show")
     * @Method("GET")
     */
    public function showAction(ManageOption $manageOption)
    {
        $deleteForm = $this->createDeleteForm($manageOption);

        return $this->render('@Inventory/ManageOption/show.html.twig', array(
            'manageOption' => $manageOption,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ManageOption entity.
     *
     * @Route("/{id}/edit", name="manage_options_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ManageOption $manageOption)
    {
        $deleteForm = $this->createDeleteForm($manageOption);
        $editForm = $this->createForm('InventoryBundle\Form\ManageOptionType', $manageOption);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($manageOption);
                $em->flush();
                $this->addFlash('notice', 'Option updated Successfully.');


                return $this->redirectToRoute('manage_options_edit', array('id' => $manageOption->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Options: ' . $e->getMessage());

                return $this->render('@Inventory/ManageOption/edit.html.twig', array(
                    'manageOption' => $manageOption,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

        }

        return $this->render('@Inventory/ManageOption/edit.html.twig', array(
            'manageOption' => $manageOption,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ManageOption entity.
     *
     * @Route("/{id}", name="manage_options_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ManageOption $manageOption)
    {
        $form = $this->createDeleteForm($manageOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($manageOption);
                $em->flush();
            }
            catch(\Exception $e) {
                return $this->redirectToRoute('manage_options_index');
            }
        }

        return $this->redirectToRoute('manage_options_index');
    }

    /**
     * Creates a form to delete a ManageOption entity.
     *
     * @param ManageOption $manageOption The ManageOption entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ManageOption $manageOption)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manage_options_delete', array('id' => $manageOption->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
