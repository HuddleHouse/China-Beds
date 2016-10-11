<?php

namespace InventoryBundle\Controller\API;

use OrderBundle\Services\LedgerService;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;


/**
 * RebateSubmission controller.
 *
 * @Route("/api")
 */
class RebateSubmissionController extends Controller
{
    /**
     * Approve/Deny Rebate Submission
     *
     * @Route("/api_approve_deny_rebate_submission", name="api_approve_deny_rebate_submission")
     * @Method({"GET", "POST"})
     */
    public function approveDenyRebateSubmissionAction(Request $request)
    {
        if($request->get('submission_id') == 0)
            return new JsonResponse(array(false, "Invalid Rebate Submission ID."));
        try {
            $em = $this->getDoctrine()->getManager();
            $rebateSubmission = $em->getRepository('InventoryBundle:RebateSubmission')->find($request->get('submission_id'));
            $rebateSubmission->setCreditIssued($request->get('credit_issued') == 1 ? true : false);
            $rebateSubmission->setAmountIssued($request->get('amount_issued'));
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
            return new JsonResponse(array(true));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

    /**
     * Show a Rebate Submission
     *
     * @Route("/api_show_rebate_submission", name="api_show_rebate_submission")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request)
    {
        if($request->get('submission_id') == 0)
            return new JsonResponse(array(false, "Invalid Rebate Submission ID."), 404);

        try {
            $rebateSubmission = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:RebateSubmission')->find($request->get('submission_id'));
            $template = $this->renderView('@Inventory/RebateSubmission/modal-show.html.twig', array(
                'rebateSubmission' => $rebateSubmission,
                'order' => $rebateSubmission->getOrder(),
            ));
            return new JsonResponse(array(true, $template));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

    /**
     * Deletes a Rebate Submission entity.
     *
     * @Route("/api_rebate_submit_delete", name="api_rebate_submit_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $rebateSubmission = $em->getRepository('InventoryBundle:RebateSubmission')->find($request->get('submission_id'));
            if($rebateSubmission->getCreditIssued() != null) {
                $this->addFlash('error', 'You many not delete submissions that have been processed.');
                return new JsonResponse(false);
            }
            $em->remove($rebateSubmission);
            $em->flush();
        }
        catch(\Exception $e) {
            $this->addFlash('error', 'Error deleting Rebate: ' . $e->getMessage());
            return new JsonResponse(false);
        }
        $this->addFlash('notice', 'Rebate Deleted successfully.');
        return new JsonResponse(array(true));
    }
}
