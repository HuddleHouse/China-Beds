<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Specification;
use InventoryBundle\Form\SpecificationType;

/**
 * Specification controller.
 *
 * @Route("/specification")
 */
class SpecificationController extends Controller
{
    /**
     * Lists all Specification entities.
     *
     * @Route("/", name="specification_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $specifications = $em->getRepository('InventoryBundle:Specification')->findAll();

        return $this->render('@Inventory/Specification/index.html.twig', array(
            'specifications' => $specifications,
        ));
    }

    /**
     * Creates a new Specification entity.
     *
     * @Route("/new", name="specification_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $specification = new Specification();
        $form = $this->createForm('InventoryBundle\Form\SpecificationType', $specification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($specification);
                $em->flush();

                $this->addFlash('notice', 'Specification created successfully.');
                
                return $this->redirectToRoute('specification_index', array('id' => $specification->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating specification: ' . $e->getMessage());
                
                return $this->render('@Inventory/Specification/new.html.twig', array(
                    'specification' => $specification,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Specification/new.html.twig', array(
            'specification' => $specification,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Specification entity.
     *
     * @Route("/{id}", name="specification_show")
     * @Method("GET")
     */
    public function showAction(Specification $specification)
    {
        $deleteForm = $this->createDeleteForm($specification);

        return $this->render('@Inventory/Specification/show.html.twig', array(
            'specification' => $specification,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Specification entity.
     *
     * @Route("/{id}/edit", name="specification_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Specification $specification)
    {
        $deleteForm = $this->createDeleteForm($specification);
        $editForm = $this->createForm('InventoryBundle\Form\SpecificationType', $specification);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($specification);
                $em->flush();

                $this->addFlash('notice', 'Specification updated successfully.');

                return $this->redirectToRoute('specification_edit', array('id' => $specification->getId())); 
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating specification: ' . $e->getMessage());
                return $this->render('@Inventory/Specification/edit.html.twig', array(
                    'specification' => $specification,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Specification/edit.html.twig', array(
            'specification' => $specification,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Specification entity.
     *
     * @Route("/{id}", name="specification_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Specification $specification)
    {
        $form = $this->createDeleteForm($specification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($specification);
                $em->flush();
                $this->addFlash('notice', 'Specification deleted successfully.');
            }
            catch(\Exception $e){
                $this->addFlash('error', 'Error deleting specification: ' . $e->getMessage());
                return $this->redirectToRoute('specification_index');
            }

        }

        return $this->redirectToRoute('specification_index');
    }

    /**
     * Creates a form to delete a Specification entity.
     *
     * @param Specification $specification The Specification entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Specification $specification)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('specification_delete', array('id' => $specification->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
