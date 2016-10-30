<?php

namespace InventoryBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Rebate controller.
 *
 * @Route("/api")
 */
class PromoKitController extends Controller
{
    /**
     * Approve or Deny a promo kit
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/api_approve_deny_promo_kit", name="api_approve_deny_promo_kit")
     * @Method({"GET", "POST"})
     */
    public function approveDenyAction(Request $request)
    {
        try {
            $promoKit = $this->getDoctrine()->getRepository('InventoryBundle:PromoKitOrders')->find($request->get('id'));
            if($request->get('approved') == 0)
                $promoKit->setApproved(false);
            elseif($request->get('approved') == 1)
                $promoKit->setApproved(true);

            $this->getDoctrine()->getEntityManager()->persist($promoKit);
            $this->getDoctrine()->getEntityManager()->flush();
            $this->addFlash('notice', 'Promo Kit Order request updated successfully.');
            return new JsonResponse(true);
        }
        catch(\Exception $e){
            return new JsonResponse("Error approving Promo Kit: ", $e->getMessage());
        }
    }

    /**
     * Show a Promo Kit Request
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/api_show_promo_kit_request", name="api_show_promo_kit_request")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request)
    {
        if($request->get('id') == 0)
            return new JsonResponse(array(false, "Invalid Promo Kit Request ID."));

        try {
            $promoKitOrder = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:PromoKitOrders')->find($request->get('id'));
            $template = $this->renderView('@Inventory/Promokit/modal-show.html.twig', array(
                'promoKitOrder' => $promoKitOrder,
                'promoKitItems' => $promoKitOrder->getPromoKitItems(),
                'products' => $promoKitOrder->getProductVariants(),
                'popItems' => $promoKitOrder->getPopItems()
            ));
            return new JsonResponse(array(true, $template));
        }
        catch(\Exception $e) {
            return new JsonResponse(array(false, $e->getMessage()));
        }
    }

}
