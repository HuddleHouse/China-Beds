<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Attribute;
use InventoryBundle\Form\AttributeType;

/**
 * Attribute controller.
 *
 * @Route("/attribute")
 */
class AttributeController extends Controller
{
    /**
     * Lists all Attribute entities.
     *
     * @Route("/", name="attribute_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $attributes = $em->getRepository('InventoryBundle:Attribute')->findAll();

        return $this->render('@Inventory/Attribute/index.html.twig', array(
            'attributes' => $attributes,
        ));
    }

    /**
     * Creates a new Attribute entity.
     *
     * @Route("/new", name="attribute_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $attribute = new Attribute();
        $form = $this->createForm('InventoryBundle\Form\AttributeType', $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $attribute->upload();
                $em->persist($attribute);
                $em->flush();
                $this->addFlash('notice', 'Attribute created successfully.');

                return $this->redirectToRoute('attribute_new');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating attribute: ' . $e->getMessage());

                return $this->render('@Inventory/Attribute/new.html.twig', array(
                    'attribute' => $attribute,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Attribute/new.html.twig', array(
            'attribute' => $attribute,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Attribute entity.
     *
     * @Route("/{id}", name="attribute_show")
     * @Method("GET")
     */
    public function showAction(Attribute $attribute)
    {
        $deleteForm = $this->createDeleteForm($attribute);

        return $this->render('@Inventory/Attribute/show.html.twig', array(
            'attribute' => $attribute,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Attribute entity.
     *
     * @Route("/{id}/edit", name="attribute_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Attribute $attribute)
    {
        $deleteForm = $this->createDeleteForm($attribute);
        $editForm = $this->createForm('InventoryBundle\Form\AttributeType', $attribute);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $attribute->upload();

                $em->persist($attribute);
                $em->flush();
                $this->addFlash('notice', 'Attribute updated successfully.');
                return $this->redirectToRoute('attribute_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error editing attribute: ' . $e->getMessage());
                return $this->render('@Inventory/Attribute/edit.html.twig', array(
                    'attribute' => $attribute,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Attribute/edit.html.twig', array(
            'attribute' => $attribute,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Attribute entity.
     *
     * @Route("/{id}", name="attribute_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Attribute $attribute)
    {
        $form = $this->createDeleteForm($attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($attribute);
                $em->flush();

                $this->addFlash('notice', 'Attribute deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting attribute: ' . $e->getMessage());
                return $this->redirectToRoute('attribute_index');
            }
        }

        return $this->redirectToRoute('attribute_index');
    }

    /**
     * Creates a form to delete a Attribute entity.
     *
     * @param Attribute $attribute The Attribute entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Attribute $attribute)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attribute_delete', array('id' => $attribute->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
