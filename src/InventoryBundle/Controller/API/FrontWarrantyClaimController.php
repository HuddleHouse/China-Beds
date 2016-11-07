<?php

namespace InventoryBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class FrontWarrantyClaimController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/front_warranty_claim", name="front_warranty_claim")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fwc = $em->getRepository('InventoryBundle:FrontWarrantyClaim')->findAll();

        return $this->render('InventoryBundle:FrontWarrantyClaim:index.html.twig', array('fwc' => $fwc));

    }

}