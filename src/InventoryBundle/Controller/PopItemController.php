<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\PopItem;
use InventoryBundle\Form\PopItemType;

/**
 * PopItem controller.
 *
 * @Route("/pop_item")
 */
class PopItemController extends Controller
{
    /**
     * Lists all PopItem entities.
     *
     * @Route("/", name="pop_item_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $popItems = $em->getRepository('InventoryBundle:PopItem')->findBy(['channel'=>$this->getUser()->getActiveChannel()]);

        return $this->render('@Inventory/Popitem/index.html.twig', array(
            'popItems' => $popItems,
        ));
    }

    /**
     * Creates a new PopItem entity.
     *
     * @Route("/new", name="pop_item_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $popItem = new PopItem();
        $form = $this->createForm('InventoryBundle\Form\PopItemType', $popItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $channel = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());
            $popItem->setChannel($channel);
            $em = $this->getDoctrine()->getManager();
            $em->persist($popItem);
            $em->flush();
            $this->addFlash('notice', 'POP Item created successfully.');

            return $this->redirectToRoute('pop_item_show', array('id' => $popItem->getId()));
        }

        return $this->render('@Inventory/Popitem/new.html.twig', array(
            'popItem' => $popItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PopItem entity.
     *
     * @Route("/{id}", name="pop_item_show")
     * @Method("GET")
     */
    public function showAction(PopItem $popItem)
    {
        $deleteForm = $this->createDeleteForm($popItem);

        return $this->render('@Inventory/Popitem/show.html.twig', array(
            'popItem' => $popItem,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PopItem entity.
     *
     * @Route("/{id}/edit", name="pop_item_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PopItem $popItem)
    {
        $deleteForm = $this->createDeleteForm($popItem);
        $editForm = $this->createForm('InventoryBundle\Form\PopItemType', $popItem);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $popItem->upload();

                $em->persist($popItem);
                $em->flush();
                $this->addFlash('notice', 'POP Item updated successfully.');
                
                return $this->redirectToRoute('pop_item_edit', array('id' => $popItem->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error editing POP Item: ' . $e->getMessage());

                return $this->render('@Inventory/Popitem/edit.html.twig', array(
                    'popItem' => $popItem,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

        }

        return $this->render('@Inventory/Popitem/edit.html.twig', array(
            'popItem' => $popItem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PopItem entity.
     *
     * @Route("/{id}", name="pop_item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PopItem $popItem)
    {
        $form = $this->createDeleteForm($popItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($popItem);
            $em->flush();
        }

        return $this->redirectToRoute('pop_item_index');
    }

    /**
     * Creates a form to delete a PopItem entity.
     *
     * @param PopItem $popItem The PopItem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PopItem $popItem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pop_item_delete', array('id' => $popItem->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
