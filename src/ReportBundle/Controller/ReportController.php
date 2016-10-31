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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

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
            'Order ID',
            'Order Number',
            'Pickup Date',
            'Ship Name',
            'User Name',
            'Address'
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
            'Address',
            'Order Amount',
            'Shipping Amount'
        );

        //2D array of data from the query for each row[column] of data
        $report['data'] = $em->getRepository('OrderBundle:Orders')->getDailyOrderReportData();
        //total to be displayed if applicable
        $report['total'] = 0;

        foreach($report['data'] as $order){
            $report['total'] += $order->getSubTotal();
        }

        if($report['data'] == array())
            $this->addFlash('notice', 'No Orders Today');

        return $this->render('ReportBundle:Reports:daily-order.html.twig', array(
            'report' => $report
        ));
    }

    /**
     * Description
     *
     * @Route("/monthly_order", name="monthly_order")
     * @Method({"GET", "POST"})
     */
    public function monthOrderAction(Request $request){

        $report = array();

        $report['headers'] = array(
            'Order ID',
            'Order Number',
            'Pickup Date',
            'Ship Name',
            'User Name',
            'Address',
            'Shipping Amount',
            'Order Amount'

        );

        $report['title'] = 'Monthly Orders';

        $d = new \DateTime();
        $d2 = new \DateTime();
        $month = $request->get('month') ? $request->get('month') : date('m');
        $d->setDate(date('Y'), $month, 01);
        $d2->setDate(date('Y'), $month, date('t'));

        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('OrderBundle:Orders');
        $query = $orders->createQueryBuilder('o')
            ->where('o.submitDate between ?0 AND ?1 ')
            ->setParameters(array($d, $d2));
        $result = $query->getQuery()->getResult();

        $report['data'] = $result;

        $report['total'] = 0;
        foreach ($report['data'] as $order){
            $report['total'] += $order->getSubtotal();
        }

        return $this->render('ReportBundle:Reports:month.html.twig', array('report' => $report, 'date' => date('Y') ));
    }


    /**
     * Description
     *
     * @Route("/best_selling", name="best_selling")
     * @Method({"GET", "POST"})
     */
    public function bestSellingAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $report = array();

        $report['headers'] = array(
            'Item',
            'Amount Ordered '
        );

        $userDate = $request->get('date') ? $request->get('month') : date('Y-m-d');


//        $orders = $em->getRepository('OrderBundle:Orders');
//        $query = $orders->createQueryBuilder('o')
//            ->where()


        return $this->render('ReportBundle:Reports:best-selling.html.twig', array('report' => $report ));
    }


    /**
     * Description
     *
     * @Route("/recent_orders", name="recent_orders")
     * @Method({"GET", "POST"})
     */
    public function recentReportAction(Request $request)
    {

        $report = array();

        $report['headers'] = array(
            'Order ID',
            'Order Number',
            'Submit Date',
            'Ship Name',
            'User Name',
            'Address',
            'Shipping Amount',
            'Order Amount'

        );

        $report['title'] = 'Recent Orders';

        $d = new \DateTime();
        $d2 = new \DateTime();
        $month = $request->get('month') ? $request->get('month') : date('m');
        $d->setDate(date('Y'), $month, 01);
        $d2->setDate(date('Y'), $month, date('t'));

        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('OrderBundle:Orders');
        $query = $orders->createQueryBuilder('o');
        $result = $query->getQuery()->getResult();

        $report['data'] = $result;

        return $this->render('ReportBundle:Reports:recent.html.twig', array('report' => $report));


    }
}

