<?php

namespace InventoryBundle\Controller;

use OrderBundle\Services\LedgerService;
use Proxies\__CG__\InventoryBundle\Entity\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\RebateSubmission;
use InventoryBundle\Form\RebateSubmissionType;

/**
 * RebateSubmission controller.
 *
 * @Route("/rebate_submissions")
 */
class RebateSubmissionController extends Controller
{
    /**
     * Lists all RebateSubmission entities.
     *
     * @Route("/", name="rebate_submit_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rebateSubmissions = $em->getRepository('InventoryBundle:RebateSubmission')->findByUser($this->getUser());

        return $this->render('@Inventory/RebateSubmission/index.html.twig', array(
            'rebateSubmissions' => $rebateSubmissions,
        ));
    }

    /**
     * Lists all RebateSubmission entities.
     *
     * @Route("/claims", name="rebate_submit_user_claims")
     * @Method({"GET", "POST"})
     */
    public function userClaimsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rebateSubmissions = $em->getRepository('InventoryBundle:RebateSubmission')->findBy(array('submittedForUser' => $this->getUser()));

        return $this->render('@Inventory/RebateSubmission/user-rebate-submissions.html.twig', array(
            'rebateSubmissions' => $rebateSubmissions,
        ));
    }

    /**
     * Creates a new RebateSubmission entity.
     *
     * @Route("/{id}/new", name="rebate_submit_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, \InventoryBundle\Entity\Channel $channel)
    {
        $rebateSubmission = new RebateSubmission();
        $rebateSubmission->setChannel($channel);

        $form = $this->createForm('InventoryBundle\Form\RebateSubmissionType', $rebateSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $channel->getRebateSubmissions()->add($rebateSubmission);
                $rebateSubmission->getRebate()->getSubmissions()->add($rebateSubmission);
                $rebateSubmission->getOrder()->getRebateSubmissions()->add($rebateSubmission);

                $rebateSubmission->setSubmittedByUser($this->getUser());
                $this->getUser()->getSubmittedRebates()->add($rebateSubmission);

                $rebateSubmission->setSubmittedForUser($rebateSubmission->getOrder()->getSubmittedForUser());
                $rebateSubmission->getSubmittedForUser()->getRebateSubmissions()->add($rebateSubmission);

                $rebateSubmission->upload();
                $em->persist($rebateSubmission);
                $em->flush();
                $this->addFlash('notice', 'Rebate submitted successfully.');
                return $this->redirectToRoute('rebate_submit_index');
            }
            catch(\Exception $e){
                $this->addFlash('error', 'Error submitting Rebate ' . $e->getMessage());
                return $this->render('@Inventory/RebateSubmission/new.html.twig', array(
                    'rebateSubmission' => $rebateSubmission,
                    'channel' => $channel,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/RebateSubmission/new.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'channel' => $channel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RebateSubmission entity.
     *
     * @Route("/{id}", name="rebate_submit_show")
     * @Method("GET")
     */
    public function showAction(Request $request, RebateSubmission $rebateSubmission)
    {
        $form = $this->createForm('InventoryBundle\Form\RebateApprovalType', $rebateSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $rebateSubmission->setDatePosted(new \DateTime());
                if($rebateSubmission->getCreditIssued()) {
                    $ledgerService = new LedgerService($this->container);
                    $ledgerService->newEntry(
                        $rebateSubmission->getAmountIssued(),
                        $rebateSubmission->getSubmittedForUser(),
                        $rebateSubmission->getSubmittedByUser(),
                        $rebateSubmission->getChannel(),
                        null,
                        'Rebate',
                        $rebateSubmission->getId()
                    );
                }
                $em->persist($rebateSubmission);
                $em->flush();
                $this->addFlash('notice', 'Rebate updated successfully.');
                return $this->redirectToRoute('rebate_submit_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Rebate ' . $e->getMessage());
                return $this->render('@Inventory/RebateSubmission/show.html.twig', array(
                    'rebateSubmission' => $rebateSubmission,
                    'order' => $rebateSubmission->getOrder(),
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/RebateSubmission/show.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'order' => $rebateSubmission->getOrder(),
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RebateSubmission entity.
     *
     * @Route("/{id}/edit", name="rebate_submit_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RebateSubmission $rebateSubmission)
    {
        $oldPath = $rebateSubmission->getPath();
        $oldRebate = $rebateSubmission->getRebate();
        $oldOrder = $rebateSubmission->getOrder();

        $editForm = $this->createForm('InventoryBundle\Form\RebateSubmissionType', $rebateSubmission);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                if($oldPath != $rebateSubmission->getPath()) {
                    $file_path = $rebateSubmission->getUploadRootDir() . '/' . $oldPath;
                    if(file_exists($file_path)) unlink($file_path);
                    $rebateSubmission->upload();
                }
                if($oldRebate != $rebateSubmission->getRebate()) {
                    $oldRebate->getSubmissions()->remove($rebateSubmission);
                    $rebateSubmission->getRebate()->getSubmissions()->add($rebateSubmission);
                }
                if($oldOrder != $rebateSubmission->getOrder()) {
                    $oldOrder->getSubmissions()->remove($rebateSubmission);
                    $rebateSubmission->getOrder()->getSubmissions()->add($rebateSubmission);
                }
                $em->persist($rebateSubmission);
                $em->flush();
                $this->addFlash('notice', 'Rebate updated successfully.');
                return $this->redirectToRoute('rebate_submit_edit', array('id' => $rebateSubmission->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Rebate ' . $e->getMessage());
                return $this->render('@Inventory/RebateSubmission/edit.html.twig', array(
                    'rebateSubmission' => $rebateSubmission,
                    'edit_form' => $editForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/RebateSubmission/edit.html.twig', array(
            'rebateSubmission' => $rebateSubmission,
            'edit_form' => $editForm->createView(),
        ));
    }
}
