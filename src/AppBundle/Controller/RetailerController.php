<?php


namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retailer controller.
 *
 * @Route("/retailer")
 */
class RetailerController extends Controller
{
      /**
     * @Route("/affiliates", name="retailer_affiliates")
     */
    public function affiliatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:Retailer:affiliates.html.twig', array(
            'users' => $users
        ));
    }

          /**
     * @Route("/get/retailer/users", name="retailer_affiliates_get_users")
     */
    public function usersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();


        return $this->render('AppBundle:Retailer:users.html.twig', array(
            'users' => $users
        ));
    }
}
