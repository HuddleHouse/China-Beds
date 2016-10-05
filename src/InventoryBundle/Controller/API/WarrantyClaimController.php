<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\WarrantyApprovalType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\WarrantyClaim;
use InventoryBundle\Form\WarrantyClaimType;
use Symfony\Component\HttpFoundation\Response;

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
        if($request->get('order_id') == '')
            return new JsonResponse('<option>Select Order ID first</option>');

        $order = $this->getDoctrine()->getRepository('OrderBundle:Orders')->find($request->get('order_id'));
        $rtn = array();

        foreach($order->getProductVariants() as $pv)
            $rtn[] = '<option value="' . $pv->getProductVariant()->getId() . '">'. $pv->getProductVariant()->getProduct()->getName() . ' ' . $pv->getProductVariant()->getName() . '</option>';

        return new JsonResponse($rtn);
    }
}
