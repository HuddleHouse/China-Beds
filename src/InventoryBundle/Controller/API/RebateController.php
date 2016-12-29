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
class RebateController extends Controller
{
    /**
     * Toggle Rebate active/inactive
     *
     * @Route("/api_change_rebate_active", name="api_change_rebate_active")
     * @Method({"GET", "POST"})
     */
    public function changeRebateActiveAction(Request $request)
    {
        try {
            $rebate = $this->getDoctrine()->getRepository('InventoryBundle:Rebate')->find($request->get('rebate_id'));
            $rebate->setActive($request->get('active') == "true" ? true : false);
            $this->getDoctrine()->getEntityManager()->persist($rebate);
            $this->getDoctrine()->getEntityManager()->flush();
            return new JsonResponse(true);
        }
        catch(\Exception $e){
            return new JsonResponse("Error changing Rebate status: ", $e->getMessage());
        }
    }

    /**
     * Deletes a Rebate entity.
     *
     * @Route("/api_rebate_delete", name="api_rebate_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $rebate = $em->getRepository('InventoryBundle:Rebate')->find($request->get('rebate_id'));
            $em->remove($rebate);
            $em->flush();
        }
        catch(\Exception $e) {
            $this->addFlash('error', 'This rebate cannot be deleted because someone has claimed it. Make it inactive if you would like no one else to be able to claim it.');
            return new JsonResponse(false);
        }
        $this->addFlash('notice', 'Rebate Deleted successfully.');
        return new JsonResponse(true);
    }
}
