<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\WarrantyApprovalType;
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

        if($this->getUser()->hasRole('ROLE_ADMIN'))
            $warrantyClaims = $em->getRepository('InventoryBundle:WarrantyClaim')->findAll();
        else
            $warrantyClaims = $em->getRepository('InventoryBundle:WarrantyClaim')->findby(array('submittedForUser' => $this->getUser()));

        return $this->render('@Inventory/WarrantyClaim/index.html.twig', array(
            'warrantyClaims' => $warrantyClaims,
            'user' => $this->getUser()
        ));
    }

    /**
     * Creates a new WarrantyClaim entity.
     *
     * @Route("/{id}/new", name="warrantyclaim_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Channel $channel)
    {
        $warrantyClaim = new WarrantyClaim();
        $form = $this->createForm(WarrantyClaimType::class, $warrantyClaim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                //set values that the form didn't
                $warrantyClaim->setSubmittedForUser($warrantyClaim->getOrder()->getSubmittedForUser());
                $warrantyClaim->setSubmittedByUser($this->getUser());
                $warrantyClaim->setChannel($channel);
                //set other side of relations
                $channel->getWarrantyClaims()->add($warrantyClaim);
                $this->getUser()->getSubmittedWarrantyClaims()->add($warrantyClaim);
                $warrantyClaim->getSubmittedForUser()->getWarrantyClaims()->add($warrantyClaim);
                $em->persist($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Warranty Claim created successfully.');
                return $this->redirectToRoute('warrantyclaim_show', array('id' => $warrantyClaim->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Warranty Claim Item: ' . $e->getMessage());
                return $this->render('@Inventory/WarrantyClaim/new.html.twig', array(
                    'warrantyClaim' => $warrantyClaim,
                    'channel' => $channel,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/WarrantyClaim/new.html.twig', array(
            'warrantyClaim' => $warrantyClaim,
            'channel' => $channel,
            'form' => $form->createView(),
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
            try {
                $em = $this->getDoctrine()->getManager();
                $warrantyClaim->setUser($this->getUser());

                $em->persist($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Warranty Claim updated successfully.');
                return $this->redirectToRoute('warrantyclaim_edit', array('id' => $warrantyClaim->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Warranty Claim Item: ' . $e->getMessage());
                return $this->render('@Inventory/WarrantyClaim/edit.html.twig', array(
                    'warrantyClaim' => $warrantyClaim,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/WarrantyClaim/edit.html.twig', array(
            'warrantyClaim' => $warrantyClaim,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Shows admins warranty claims so they can approve or deny them.
     *
     * @Route("/{id}", name="warrantyclaim_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, WarrantyClaim $warrantyClaim)
    {
        $form = $this->createForm(WarrantyApprovalType::class, $warrantyClaim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $ledger->setCreditedByUser($this->getUser());
                $this->getUser()->getCreditedLedgers()->add($ledger);
                $ledger->setDatePosted(new \DateTime());
                $ledger->setIsArchived(true);
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating credit request entry: ' . $e->getMessage());
                return $this->render('@Inventory/WarrantyClaim/show.html.twig', array(
                    'warranty_claim' => $warrantyClaim,
                    'order' => $warrantyClaim->getOrder(),
                    'form' => $form->createView(),
                ));
            }

            $this->addFlash('notice', 'Credit posted.');
            return $this->redirectToRoute('warrantyclaim_index');
        }

        return $this->render('@Inventory/WarrantyClaim/show.html.twig', array(
            'warranty_claim' => $warrantyClaim,
            'order' => $warrantyClaim->getOrder(),
            'form' => $form->createView()
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
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Warranty Claim deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Warranty Claim Item: ' . $e->getMessage());
                return $this->redirectToRoute('warrantyclaim_index');
            }
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
