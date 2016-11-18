<?php

namespace InventoryBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class FrontWarrantyClaimController
 * @package InventoryBundle\Controller\API
 */
class FrontWarrantyClaimController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/front_warranty_claim/{id}", name="front_warranty_claim")
     * @Method("GET")
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fwc = $em->getRepository('InventoryBundle:FrontWarrantyClaim')->findAll();

        return $this->render('InventoryBundle:FrontWarrantyClaim:index.html.twig', array('fwc' => $fwc, 'id'=>$id));

    }

    /**
     * Show a Warranty Claim
     *
     * @Route("/show_front_warranty_claim", name="show_front_warranty_claim")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request)
    {
        if($request->get('claim_id') == 0)
            return new JsonResponse(array(false, "Invalid Rebate Submission ID."));

        try {
            $frontWarrantyClaim = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:FrontWarrantyClaim')->find($request->get('claim_id'));

            $template = $this->renderView("InventoryBundle:FrontWarrantyClaim:modal-show.html.twig", array(
                'warranty_claim' => $frontWarrantyClaim,
//                'order' => $warrantyClaim->getOrder(),
            ));
            return new JsonResponse(array(true, $template));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }


    /**
     * Approve/Deny Warranty Claim
     *
     * @Route("/front_approve_deny_warranty_claim", name="front_approve_deny_warranty_claim")
     * @Method({"GET", "POST"})
     */
    public function approveDenyWarrantyClaimAction(Request $request)
    {
        $tmp='';
        if($request->get('claim_id') == '0') {
            return new JsonResponse(array(false, "Invalid Warranty Claim ID."));
        }else {
            try {
                $em = $this->getDoctrine()->getManager();
                $warrantyClaim = $em->getRepository('InventoryBundle:FrontWarrantyClaim')->find($request->get('claim_id'));
                $warrantyClaim->setResolution($request->get('resolution'));
                $warrantyClaim->setArchived(true);
                $em->persist($warrantyClaim);
                $em->flush();
                $this->addFlash('notice', 'Rebate updated successfully.');
                return new JsonResponse(array(true));
            } catch (\Exception $e) {
                return new JsonResponse(array(false, $e->getMessage()));
            }
        }
    }

}