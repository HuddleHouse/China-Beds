<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\PriceGroup;
use AppBundle\Form\PriceGroupType;

/**
 * PriceGroup controller.
 *
 * @Route("/admin/price_group")
 */
class PriceGroupController extends Controller
{
    /**
     * Lists all PriceGroup entities.
     *
     * @Route("/", name="admin_price_group_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $priceGroups = $em->getRepository('AppBundle:PriceGroup')->findAll();

        return $this->render('AppBundle:PriceGroup:index.html.twig', array(
            'priceGroups' => $priceGroups,
        ));
    }

    /**
     * Creates a new PriceGroup entity.
     *
     * @Route("/new", name="admin_price_group_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $priceGroup = new PriceGroup();
        $form = $this->createForm('AppBundle\Form\PriceGroupType', $priceGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($priceGroup);
            $em->flush();

            $this->addFlash('notice', 'Price Group added successfully.');
            return $this->redirectToRoute('admin_price_group_index');
        }

        return $this->render('AppBundle:PriceGroup:new.html.twig', array(
            'priceGroup' => $priceGroup,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PriceGroup entity.
     *
     * @Route("/{id}", name="admin_price_group_show")
     * @Method("GET")
     */
    public function showAction(PriceGroup $priceGroup)
    {
        $deleteForm = $this->createDeleteForm($priceGroup);

        return $this->render('AppBundle:PriceGroup:show.html.twig', array(
            'priceGroup' => $priceGroup,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PriceGroup entity.
     *
     * @Route("/{id}/edit", name="admin_price_group_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PriceGroup $priceGroup)
    {
        $deleteForm = $this->createDeleteForm($priceGroup);
        $editForm = $this->createForm('AppBundle\Form\PriceGroupType', $priceGroup);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($priceGroup);
            $em->flush();

            $this->addFlash('notice', 'Price Group updated successfully.');
            return $this->redirectToRoute('admin_price_group_index');
        }

        return $this->render('AppBundle:PriceGroup:edit.html.twig', array(
            'priceGroup' => $priceGroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PriceGroup entity.
     *
     * @Route("/{id}", name="admin_price_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PriceGroup $priceGroup)
    {
        $form = $this->createDeleteForm($priceGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($priceGroup);
            $em->flush();
        }

        return $this->redirectToRoute('admin_price_group_index');
    }

    /**
     * Creates a form to delete a PriceGroup entity.
     *
     * @param PriceGroup $priceGroup The PriceGroup entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PriceGroup $priceGroup)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_price_group_delete', array('id' => $priceGroup->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
