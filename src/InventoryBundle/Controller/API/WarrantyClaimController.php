<?php

namespace InventoryBundle\Controller\API;

use AppBundle\Entity\User;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\WarrantyApprovalType;
use OrderBundle\Entity\Ledger;
use OrderBundle\Entity\Orders;
use OrderBundle\Services\LedgerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\WarrantyClaim;
use InventoryBundle\Form\WarrantyClaimType;
use Symfony\Component\HttpFoundation\Response;
use WarehouseBundle\Entity\Status;

/**
 * WarrantyClaim controller.
 *
 * @Route("/api")
 */
class WarrantyClaimController extends Controller
{
    /**
     * Get ProductVariants associated with an order
     *
     * @Route("/api_get_product_variants_from_order", name="api_get_product_variants_from_order")
     * @Method({"GET", "POST"})
     */
    public function getProductVariantsFromOrderAction(Request $request)
    {
//        if($request->get('order_id') == '')
//            return new JsonResponse('<option>Select Order ID first</option>');
        if ( $request->get('order_id') == '' ) {
            $products = $this->getDoctrine()->getRepository('InventoryBundle:Product')->getAllProductsArray($this->getUser());

            $rtn = array();
            $rtn[] = "<option selected disabled>Select Product</option>";
            foreach($products as $product)
                $rtn[] = '<option value="' . $product['id'] . '">'. $product['name'] . '</option>';
        } else {
            $order = $this->getDoctrine()->getRepository('OrderBundle:Orders')->find($request->get('order_id'));
            $rtn = array();
            $rtn[] = "<option selected disabled>Select Product</option>";
            foreach($order->getProductVariants() as $pv)
                $rtn[] = '<option value="' . $pv->getProductVariant()->getId() . '">'. $pv->getProductVariant()->getProduct()->getName() . ' ' . $pv->getProductVariant()->getName() . '</option>';
        }


        return new JsonResponse($rtn);
    }

    /**
     * Get Orders associated with a User
     *
     * @Route("/api_get_orders_from_user", name="api_get_orders_from_user")
     * @Method({"GET", "POST"})
     */
    public function getOrdersFromUser(Request $request)
    {
//        if($request->get('order_id') == '')
//            return new JsonResponse('<option>Select Order ID first</option>');

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('user_id'));

        $rtn = array();
        $rtn[] = "<option selected disabled>Select Order Id</option>";
        foreach($user->getOrders() as $order) {
            if ( !$order->getStatus()->getWarrantyAllowed() ) { continue; }
            if ( $order->getChannel()->getId() == $this->getUser()->getActiveChannel()->getId() ) {
                $rtn[] = sprintf('<option value="%d">%s</option>', $order->getId(), $order->getOrderId());
            }
        }

        return new JsonResponse($rtn);
    }

    /**
     * Approve/Deny Warranty Claim
     *
     * @Route("/api_approve_deny_warranty_claim", name="api_approve_deny_warranty_claim")
     * @Method({"GET", "POST"})
     */
    public function approveDenyWarrantyClaimAction(Request $request)
    {
        if($request->get('claim_id') == 0)
            return new JsonResponse(array(false, "Invalid Warranty Claim ID."));
        try {
            $em = $this->getDoctrine()->getManager();
            $warrantyClaim = $em->getRepository('InventoryBundle:WarrantyClaim')->find($request->get('claim_id'));
            $warrantyClaim->setResolution($request->get('resolution'));
            if($request->get('apply_credit') == 1) {
                $ledgerService = new LedgerService($this->container);
                $ledgerService->newEntry(
                    $request->get('amount'),
                    $warrantyClaim->getSubmittedForUser(),
                    $warrantyClaim->getSubmittedByUser(),
                    $warrantyClaim->getChannel(),
                    null,
                    Ledger::TYPE_CLAIM,
                    $warrantyClaim->getId(),
                    false,
                    true
                );
            }
            $warrantyClaim->setIsArchived(true);
            $em->persist($warrantyClaim);
            $em->flush();
            $this->addFlash('notice', 'Rebate updated successfully.');
            return new JsonResponse(array(true));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }



    /**
     * Show a Warranty Claim
     *
     * @Route("/api_show_warranty_claim", name="api_show_warranty_claim")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request)
    {
        if($request->get('claim_id') == 0)
            return new JsonResponse(array(false, "Invalid Rebate Submission ID."));

        try {
            $warrantyClaim = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:WarrantyClaim')->find($request->get('claim_id'));
            if($warrantyClaim->getDateMadeAware() == null) {
                $warrantyClaim->setDateMadeAware(new \DateTime());
                $this->getDoctrine()->getManager()->persist($warrantyClaim);
                $this->getDoctrine()->getManager()->flush();
            }
            $template = $this->renderView('@Inventory/WarrantyClaim/modal-show.html.twig', array(
                'warranty_claim' => $warrantyClaim,
                'order' => $warrantyClaim->getOrder(),
            ));
            return new JsonResponse(array(true, $template));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }
}
