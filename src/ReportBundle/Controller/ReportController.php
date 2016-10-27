<?php

namespace ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/")
     */
    public function indexAction() {
        return $this->render('ReportBundle:Default:index.html.twig');
    }

    /**
     * Description
     *
     * @Route("/template", name="")
     * @Method({"GET", "POST"})
     */
    public function templateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        /*
         * note: any cell can be left null
         */
        $report = array();
        //string for page title
        $report['title'] = 'Report Template';
        //array of string to go in the table headers
        $report['headers'] = array(
            ''
        );
        //2D array of data from the query for each row[column] of data
        $report['data'] = $em->getRepository('OrderBundle:Orders')->getTemplateReportData();
        //array of totals to be displayed if applicable
        $report['totals'] = array();

        return $this->render('ReportBundle:Reports:report-base.html.twig', array(
            'report' => $report
        ));
    }

    /**
     * Description
     *
     * @Route("/daily_order", name="daily_order")
     * @Method({"GET", "POST"})
     */
    public function dailyOrderAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        /*
         * note: any cell can be left null
         */
        $report = array();
        //string for page title
        $report['title'] = 'Daily Order Report';
        //array of string to go in the table headers
        $report['headers'] = array(
            'Order ID',
            'Order Number',
            'Pickup Date',
            'Ship Name',
            'User Name',
            'Address'
        );

        //2D array of data from the query for each row[column] of data
        $report['data'] = $em->getRepository('OrderBundle:Orders')->getDailyOrderReportData();
        //array of totals to be displayed if applicable
        $report['totals'] = array();

        if($report['data'] == array())
            $this->addFlash('notice', 'No Orders Today');
        return $this->render('ReportBundle:Reports:report-base.html.twig', array(
            'report' => $report
        ));
    }
}
