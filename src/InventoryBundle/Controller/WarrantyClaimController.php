<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\WarrantyApprovalType;
use OrderBundle\Entity\Ledger;
use OrderBundle\Entity\Orders;
use OrderBundle\Services\LedgerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\WarrantyClaim;
use InventoryBundle\Form\WarrantyClaimType;
use Symfony\Component\Validator\Constraints\DateTime;

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

        $warrantyClaims = $em->getRepository('InventoryBundle:WarrantyClaim')->findByUser($this->getUser());

        return $this->render('@Inventory/WarrantyClaim/index.html.twig', array(
            'warranty_claims' => $warrantyClaims,
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
                if ( empty($warrantyClaim->getFile2()) ) {
                    throw new \Exception("You must have at least 2 images uploaded.");
                }

                $em = $this->getDoctrine()->getManager();
                //set values that the form didn't
                if($warrantyClaim->getOrder() != null)
                    $warrantyClaim->setSubmittedForUser($warrantyClaim->getOrder()->getSubmittedForUser());
                else
                    $warrantyClaim->setSubmittedForUser($this->getUser());
                $warrantyClaim->setSubmittedByUser($this->getUser());
                $warrantyClaim->setChannel($channel);
                //set other side of relations
                $channel->getWarrantyClaims()->add($warrantyClaim);
                $this->getUser()->getSubmittedWarrantyClaims()->add($warrantyClaim);
                $warrantyClaim->getSubmittedForUser()->getWarrantyClaims()->add($warrantyClaim);
                $warrantyClaim->upload1();
                $warrantyClaim->upload2();
                $warrantyClaim->upload3();
                $warrantyClaim->upload4();
                $warrantyClaim->upload5();
                $em->persist($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Warranty Claim created successfully.');
                
         
                $this->get('email_service')->sendWarrantyClaimAcknowledgementEmail($this->getUser(), $warrantyClaim);
                
                
                return $this->redirectToRoute('warrantyclaim_edit', array('id' => $warrantyClaim->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Warranty Claim Item: ' . $e->getMessage());
                return $this->render('@Inventory/WarrantyClaim/new.html.twig', array(
                    'warranty_claim' => $warrantyClaim,
                    'channel' => $channel,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/WarrantyClaim/new.html.twig', array(
            'warranty_claim' => $warrantyClaim,
            'channel' => $channel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WarrantyClaim entity.
     *
     * @Route("/{id}/edit", name="warrantyclaim_edit")
     * @Method({"GET", "POST", "PATCH"})
     */
    public function editAction(Request $request, WarrantyClaim $warrantyClaim)
    {
        if ( !$this->getUser()->hasRole('ROLE_ADMIN') ) {
            return $this->redirectToRoute('warrantyclaim_index');
        }
        $oldPath1 = $warrantyClaim->getPath1();
        $oldPath2 = $warrantyClaim->getPath2();
        $oldPath3 = $warrantyClaim->getPath3();
        $oldOrder = $warrantyClaim->getOrder();

        $editForm = $this->createForm('InventoryBundle\Form\WarrantyClaimType', $warrantyClaim, array('method' => 'PATCH'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                if($oldPath1 != $warrantyClaim->getPath1()) {
                    $file_path = $warrantyClaim->getUploadRootDir() . '/' . $oldPath1;
                    if(file_exists($file_path)) unlink($file_path);
                    $warrantyClaim->upload1();
                }
                if($oldPath2 != $warrantyClaim->getPath2()) {
                    $file_path = $warrantyClaim->getUploadRootDir() . '/' . $oldPath2;
                    if(file_exists($file_path)) unlink($file_path);
                    $warrantyClaim->upload2();
                }
                if($oldPath3 != $warrantyClaim->getPath3()) {
                    $file_path = $warrantyClaim->getUploadRootDir() . '/' . $oldPath3;
                    if(file_exists($file_path)) unlink($file_path);
                    $warrantyClaim->upload3();
                }
                if($oldOrder != $warrantyClaim->getOrder()) {
                    $oldOrder->getWarrantyClaims()->remove($warrantyClaim);
                    $warrantyClaim->getOrder()->getWarrantyClaims()->add($warrantyClaim);
                }
                $em->persist($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Warranty Claim updated successfully.');
                return $this->redirectToRoute('warrantyclaim_edit', array('id' => $warrantyClaim->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Warranty Claim Item: ' . $e->getMessage());
                return $this->render('@Inventory/WarrantyClaim/edit.html.twig', array(
                    'warranty_claim' => $warrantyClaim,
                    'edit_form' => $editForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/WarrantyClaim/edit.html.twig', array(
            'warranty_claim' => $warrantyClaim,
            'edit_form' => $editForm->createView(),
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
                if($warrantyClaim->getDateMadeAware() == null)
                    $warrantyClaim->setDateMadeAware(new \DateTime());
                $applyCredit = $form->get('applyCredit')->getNormData();
                $amount = $form->get('amount')->getNormData();
                if($applyCredit) {
                    $service = new LedgerService($this->get('service_container'));
                    $service->newEntry(
                        $amount,
                        $warrantyClaim->getSubmittedForUser(),
                        $warrantyClaim->getSubmittedByUser(),
                        $warrantyClaim->getChannel(),
                        $warrantyClaim->getDescription(),
                        Ledger::TYPE_CLAIM,
                        $warrantyClaim->getId(),
                        false,
                        true
                    );
                }
                $warrantyClaim->setIsArchived(true);
                $em->persist($warrantyClaim);
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

        if(!$warrantyClaim->getDateMadeAware()) {
            $em = $this->getDoctrine()->getManager();
            $warrantyClaim->setDateMadeAware(new \DateTime());
            $em->persist($warrantyClaim);
            $em->flush();
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
