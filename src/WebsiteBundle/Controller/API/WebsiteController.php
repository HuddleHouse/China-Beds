<?php

namespace WebsiteBundle\Controller\API;

use GuzzleHttp\Client;
use InventoryBundle\Entity\FrontWarrantyClaim;
use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebsiteBundle\Controller\BaseController;

class WebsiteController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function retailerFormAction(Request $request)
    {
//        if($request->get('g-recaptcha-response') == '')
//            return new JsonResponse(array(false, 'BAIL'));

        $form_data = $request->getContent();
        $tmp = '';

        $guzzle = new Client();
        $options = array(
            'secret' => $this->getParameter('retailer_recaptcha_secret'),
            'response' => $request->get('g-recaptcha-response')
        );
        $response = $guzzle->request(
            'POST',
            'https://www.google.com/recaptcha/api/siteverify',
            $options
        );

        $recaptcha = \json_decode($response->getBody()->getContents());
        $recaptcha = get_object_vars($recaptcha);
        $ddd = '';

        if(!$recaptcha['success']) {
            $msg = 'Recaptcha failure: ';
            foreach ($recaptcha['error-codes'] as $code) {
                $tmp = str_replace('-', ' ', $code);
                $msg .= $tmp . ', ';
            }
            $msg = rtrim($msg, ',');

            return new JsonResponse(array(false, $msg));
        }

        return new JsonResponse(array(true));
    }
}
