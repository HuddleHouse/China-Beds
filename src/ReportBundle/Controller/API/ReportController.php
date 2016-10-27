<?php

namespace ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/reports")
 */
class ReportController extends Controller
{

    /**
     * Returns monthly report data.
     *
     * @Route("/api_monthly_report", name="api_monthly_report")
     * @Method({"GET", "POST"})
     */
    public function getMonthReportData()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('OrderBundle:Orders');
        $query = $orders->createQueryBuilder('o')
            ->select('o.orderId', 'o.orderNumber', 'o.pickUpDate', 'o.shipName', 'u.first_name', 'u.last_name', 'o.shipAddress', 'o.amount_paid')
            ->leftJoin('o.submitted_by_user', 'u')
            ->where('o.submitDate between :first AND :last ');
        $query->setParameters(array('first'=>date('Y-m-01'), 'last'=>date('Y-m-d')));
        $result = $query->getResult();




    }
}





















}