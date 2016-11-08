<?php

namespace WebsiteBundle\Controller\API;

use AppBundle\Entity\User;
use GuzzleHttp\Client;
use InventoryBundle\Entity\FrontWarrantyClaim;
use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use WebsiteBundle\Controller\BaseController;

class WebsiteController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function retailerFormAction(Request $request)
    {
        $guzzle = new Client();
        $options = array(
            'form_params' => array(
                'secret' => $this->getParameter('retailer_recaptcha_secret'),
                'response' => $request->get('g-recaptcha-response'),
                'remoteip' => $request->getClientIp()
            )
        );
        $response = $guzzle->request('POST', 'https://www.google.com/recaptcha/api/siteverify', $options);
        $recaptcha = \json_decode($response->getBody()->getContents(), true);

        if(!$recaptcha['success']) {
//            $msg = 'Recaptcha failure: ';
//            foreach ($recaptcha['error-codes'] as $code) {
//                $tmp = str_replace('-', ' ', $code);
//                $msg .= $tmp . ', ';
//            }
//            return new JsonResponse(array(false, rtrim($msg, ',')));
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $request->get('username')));

        if($user != null) {
            if($request->get('currentRetailer') == 1) {
//                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
//                $this->get("security.token_storage")->setToken($token); //now the user is logged in
//
//                //now dispatch the login event
//                $event = new InteractiveLoginEvent($request, $token);
//                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            }
            else
                return new JsonResponse(array(false, 'username not found'));

            //until we deal with signing them in
            return new JsonResponse(array(false, 'username taken'));
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $channel = $em->getRepository('InventoryBundle:Channel')->find(1);
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Retailer'));
            $distributor = $this->getParameter('default_distributor') ? $em->getRepository('AppBundle:User')->find($this->getParameter('default_distributor')) : null;
            $sales_rep = $this->getParameter('default_sales_rep') ? $em->getRepository('AppBundle:User')->find($this->getParameter('default_sales_rep')) : null;
            $sales_manager = $this->getParameter('default_sales_manager') ? $em->getRepository('AppBundle:User')->find($this->getParameter('default_sales_manager')) : null;

            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->createUser();

            //email admins/whomever

            $name_array = explode(' ', $request->get('name'), 2);
            $user->setFirstName($name_array[0]);
            $user->setLastName(array_key_exists(1, $name_array) ? $name_array[1] : null);
            $user->setCompanyName($request->get('company'));
            $user->setIsCurrentRetailer($request->get('currentRetailer') == '1' ? true : false);
            $user->setUsername($request->get('username'));
            $user->setEmail($request->get('email'));
            $user->setAddress1($request->get('address1'));
            $user->setAddress2($request->get('address2'));
            $user->setCity($request->get('city'));
            $user->setState($em->getRepository('AppBundle:State')->findOneBy(array('abbreviation' => strtoupper($request->get('state')))));
            $user->setZip($request->get('zip'));
            $user->setIsResidential($request->get('residential') == '1' ? true : false);
            $user->setIsOnlineIntentions($request->get('online') == '1' ? true : false);
            $user->setOnlineWebUrl($request->get('url'));
            $user->setPlainPassword($request->get('pw'));

            try {
                if($distributor != null) {
                    $user->setMyDistributor($distributor);
                    $distributor->addRetailer($user);
                    $em->persist($distributor);
                }

                if($sales_rep != null) {
                    $user->setMySalesRep($sales_rep);
                    $sales_rep->addRetailer($user);
                    $em->persist($sales_rep);
                }

                if($sales_manager != null) {
                    $user->setMySalesManager($sales_manager);
                    $sales_manager->addRetailer($user);
                    $em->persist($sales_manager);
                }

                $user->addUserChannel($channel);
                $user->addGroup($role);
                $role->addUser($user);
                $em->persist($role);

                $userManager->updateUser($user);
                $em->flush();
                return new JsonResponse(array(true, 'User creation successful, waiting for admin approval. You will be notified of the decision.'));
            }
            catch(\Exception $e) {
                return new JsonResponse(array(false, "There was an error creating the user, please try again later."));
            }        
        }
    }
}
