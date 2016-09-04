<?php

namespace OrderBundle\Controller\API;

use InventoryBundle\Entity\PurchaseOrder;
use InventoryBundle\Entity\PurchaseOrderProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class OrderProductsController extends Controller
{

    /**
     * @Route("/api_save_products_order_form", name="api_save_products_order_form")
     */
    public function saveProductsOrderForm(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel_id = $request->request->get('channel_id');
        $channel = $em->getRepository('InventoryBundle:Channel')->find($channel_id);

        $cart = $request->request->get('cart');
        $total = $request->request->get('total');
        $info = $request->request->get('form_info');
        $products = array();

        foreach($cart as $item) {
            if($item != '') {
                $image_url = '/';
//                foreach($prod->getImages() as $image) {
//                    $image_url .= $image->getWebPath();
//                    break;
//                }
//
//                foreach($prod->getVariants() as $variant)
//                    $products[] = array(
//                        'name' => $prod->getName().": ".$variant->getName(),
//                        'id' => $variant->getId(),
//                        'image_url' => $image_url
//                    );
            }

        }

        return JsonResponse::create(true);
    }

}

