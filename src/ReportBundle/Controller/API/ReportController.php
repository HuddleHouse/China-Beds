<?php

namespace ReportBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api")
 */
class ReportController extends Controller
{
    /**
     * Returns all retailers for selects.
     *
     * @Route("/api_get_retailers", name="api_get_retailers")
     * @Method("GET")
     */
    public function getRetailersAction()
    {
        $rtn = '<option>Select a Retailer</option>';
        foreach($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:User')->getAllRetailersArray($this->getUser()->getActiveChannel()) as &$retailer)
            if($retailer != null && null != $retailer->getId())
                $rtn .= '<option value="'.$retailer->getId().'">'.$retailer->getDisplayName().'</option>';
        return new JsonResponse($rtn);
    }

    /**
     * Returns all distributors selects.
     *
     * @Route("/api_get_distributors", name="api_get_distributors")
     * @Method("GET")
     */
    public function getDistributorsAction()
    {
        $rtn = '<option>Select a Distributor</option>';
        foreach($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:User')->getAllDistributorsArray() as &$distributor)
            if($distributor != null && null != $distributor->getId())
                $rtn .= '<option value="'.$distributor->getId().'">'.$distributor->getDisplayName().'</option>';
        return new JsonResponse($rtn);
    }
}