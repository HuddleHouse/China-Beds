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
     * Returns monthly report data.
     *
     * @Route("/api_monthly_report", name="api_monthly_report")
     * @Method({"GET", "POST"})
     */
    public function getMonthReportData(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


    }











}