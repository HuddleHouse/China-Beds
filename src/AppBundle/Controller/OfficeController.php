<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Office;
use AppBundle\Form\OfficeType;

/**
 * Office controller.
 *
 * @Route("/office")
 */
class OfficeController extends Controller
{
    /**
     * Lists all Office entities.
     *
     * @Route("/", name="office_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $offices = $em->getRepository('AppBundle:Office')->findAll();
        foreach($offices as $group) {
            $data[] = array(
                'name' => $group->getName(),
                'count' => count($group->getUsers()),
                'id' => $group->getId()
            );
        }
        
        return $this->render('AppBundle:Office:index.html.twig', array(
            'offices' => $data
        ));
    }

    /**
     * Creates a new Office entity.
     *
     * @Route("/new", name="office_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $office = new Office();
        $form = $this->createForm('AppBundle\Form\OfficeType', $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($office);
            $em->flush();

            return $this->redirectToRoute('office_show', array('id' => $office->getId()));
        }

        return $this->render('AppBundle:Office:new.html.twig', array(
            'office' => $office,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Office entity.
     *
     * @Route("/{id}", name="office_show")
     * @Method("GET")
     */
    public function showAction(Office $office)
    {
        $deleteForm = $this->createDeleteForm($office);

        return $this->render('AppBundle:Office:show.html.twig', array(
            'users' => $office->getUsers(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Office entity.
     *
     * @Route("/{id}/edit", name="office_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Office $office)
    {
        $deleteForm = $this->createDeleteForm($office);
        $editForm = $this->createForm('AppBundle\Form\OfficeType', $office);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($office);
            $em->flush();
            $this->addFlash('notice', 'Office updated successfully.');

            return $this->redirectToRoute('office_index');
        }

        return $this->render('AppBundle:Office:edit.html.twig', array(
            'office' => $office,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Office entity.
     *
     * @Route("/{id}", name="office_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Office $office)
    {
        $form = $this->createDeleteForm($office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($office);
            $em->flush();
        }

        return $this->redirectToRoute('office_index');
    }

    /**
     * Creates a form to delete a Office entity.
     *
     * @param Office $office The Office entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Office $office)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('office_delete', array('id' => $office->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
