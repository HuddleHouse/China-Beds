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

        if($this->getUser()->hasRole('ROLE_ADMIN') || $this->getUser()->hasRole('ROLE_SALES_MANAGER'))
            $rebates = $em->getRepository('InventoryBundle:Rebate')->findBy(['channel' => $this->getUser()->getActiveChannel()]);
        else
            $rebates = $em->getRepository('InventoryBundle:Rebate')->findBy((array('active' => true,'channel' => $this->getUser()->getActiveChannel())));

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
        return $this->render('@Inventory/Rebate/show.html.twig', array(
            'rebate' => $rebate,
            'rebateSubmissions' => $this->getDoctrine()->getRepository('InventoryBundle:RebateSubmission')->findBy(array('rebate' => $rebate))
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
                ));
            }
        }

        return $this->render('@Inventory/Rebate/edit.html.twig', array(
            'rebate' => $rebate,
            'edit_form' => $editForm->createView(),
        ));
    }
}
