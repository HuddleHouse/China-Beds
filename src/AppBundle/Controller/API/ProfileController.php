<?php

namespace AppBundle\Controller\API;

use AppBundle\Form\NewUserType;
use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersProductVariant;
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
class ProfileController extends Controller
{

    /**
     * @Route("/api_create_new_user", name="api_create_new_user")
     */
    public function createNewUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $new_user_form = $this->createForm(NewUserType::class);
//        if ( $request->isMethod( 'POST' ) ) {
//
//            $form->handleRequest($request);
//
//            if ($form->isSubmitted() && $form->isValid()) {
//
//                $userManager->updateUser($user);
//                // ... perform some action, such as saving the task to the database
//                // for example, if Task is a Doctrine entity, save it!
//                // $em = $this->getDoctrine()->getManager();
//                // $em->persist($task);
//                // $em->flush();
//
//                return $this->redirectToRoute('task_success');
//            }
//
//
//            return new JsonResponse( $response );
//        }
//
//        return array('postform' => $new_user_form->createView( ));
//
//        return JsonResponse::create($order->getId());
//    }



}

