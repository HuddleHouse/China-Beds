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
        $report['data'] = $em->getRepository('OrderBundle:Orders')->getDailyOrderReportData($this->getUser());
        //total to be displayed if applicable
        $report['total'] = 0;

        foreach($report['data'] as $order){
            $report['total'] += $order->getSubTotal();
        }

        if($report['data'] == array())
            $this->addFlash('notice', 'No Orders Today');

        return $this->render('ReportBundle:Reports:daily-order.html.twig', array(
            'report' => $report,
            'channel' => $this->getUser()->getActiveChannel()

        ));
    }

    /**
     * General monthly order report
     *
     * @Route("/monthly_order", name="monthly_order")
     * @Method({"GET", "POST"})
     */
    public function monthlyOrderAction(Request $request){

        $report = array();

        $report['title'] = 'Monthly Order Report';

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

        if ( $this->getUser()->hasRole('ROLE_RETAILER') || $this->getUser()->hasRole('ROLE_DISTRIBUTOR') ) {
            $query->andWhere('o.submitted_for_user = :user_id')
                ->setParameter('user_id', $this->getUser()->getId());
        }

        $result = $query->getQuery()->getResult();

        $report['data'] = $result;

        $report['total'] = 0;
        foreach ($report['data'] as $order){
            $report['total'] += $order->getSubtotal();
        }

        return $this->render('ReportBundle:Reports:month.html.twig', array('report' => $report, 'date' => date('Y') ));
    }

    /**
     * recent orders w/tracking
     *
     * @Route("/recent_orders", name="recent_orders")
     * @Method({"GET", "POST"})
     */
    public function recentOrderAction(Request $request){
        $report = array();
        $report['title'] = 'Recent Orders';
        $report['headers'] = array(
            'Order ID',
            'Order Number',
            'Pickup Date',
            'Ship Name',
            'User Name',
            'Address',
            'Shipping Amount',
            'Order Amount',
            'Shipping Info/Tracking Numbers'
        );

        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('OrderBundle:Orders');
        $query = $orders->createQueryBuilder('o')
            ->where('o.submitted_for_user = :user')
            ->orderBy('o.submitDate', 'DESC')
            ->setParameter('user', $this->getUser());
        $result = $query->getQuery()->getResult();

        $report['data'] = $result;

        $report['total'] = 0;
        foreach ($report['data'] as $order){
            $report['total'] += $order->getSubtotal();
        }

        return $this->render('ReportBundle:Reports:recent-orders.html.twig', array('report' => $report, 'date' => date('Y') ));
    }

    /**
     * recent orders w/tracking for sales
     *
     * @Route("/all_recent_orders", name="all_recent_orders")
     * @Method({"GET", "POST"})
     */
    public function allRecentOrdersAction(Request $request){
        $report = array();
        $report['title'] = 'All Recent Orders';
        $report['headers'] = array(
            'Order ID',
            'Order Number',
            'Pickup Date',
            'Ship Name',
            'User Name',
            'Address',
            'Shipping Amount',
            'Order Amount',
            'Shipping Info/Tracking Numbers'
        );

        $date = new \DateTime();
        $date = $date->sub(new \DateInterval('P1W'));

        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('OrderBundle:Orders');
        $query = $orders->createQueryBuilder('o')
            ->orderBy('o.submitDate', 'DESC')
            ->where('o.submitDate >= :aweekago')
            ->setParameter('aweekago', $date);
        $result = $query->getQuery()->getResult();

        $report['data'] = $result;

        $report['total'] = 0;
        foreach ($report['data'] as $order){
            $report['total'] += $order->getSubtotal();
        }

        return $this->render('ReportBundle:Reports:recent-orders.html.twig', array('report' => $report, 'date' => date('Y') ));
    }

    /**
     * Retailer and Distributor Ledger Reports for Accounting
     *
     * @Route("/accounting_ledger", name="accounting_ledger")
     * @Method({"GET", "POST"})
     */
    public function ledgerReportAction(Request $request){
        if($request->get('uid') != null) {
            $user = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:User')->find($request->get('uid'));
            $report = array();
            $report['title'] = $user->getFullName() . ' Ledger Report';
            $report['headers'] = array(
                'Type',
                'Date',
                'User',
                'ACH Status',
                'Amount'
            );
            $report['data'] = $this->getDoctrine()->getEntityManager()->getRepository('OrderBundle:Ledger')->findBy(array('submittedForUser' => $request->get('uid')), array('dateCreated' => 'DESC'));
            $total = $user->getLedgerTotal($this->getUser()->getActiveChannel()->getId());
            return $this->render('ReportBundle:Reports:ledger.html.twig', array('report' => $report, 'total' => $total, 'user' => $user));
        }

        return $this->render('ReportBundle:Reports:ledger.html.twig', array());
    }

    /**
     * Retailer and Distributor Price Lists for Accounting
     *
     * @Route("/price_list", name="price_list")
     * @Method({"GET", "POST"})
     */
    public function priceListAction(Request $request){
        if($request->get('uid') != null) {
            $user = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:User')->find($request->get('uid'));
            $report = array();
            $report['title'] = $user->getFullName() . ' Price List';
            $report['headers'] = array(
                'Product',
                'Price'
            );
            $report['data'] = $this->getDoctrine()->getEntityManager()->getRepository('InventoryBundle:Channel')->getProductArrayForChannel($this->getUser()->getActiveChannel(), $user);
            return $this->render('ReportBundle:Reports:price-list.html.twig', array('report' => $report, 'user' => $user));
        }

        return $this->render('ReportBundle:Reports:price-list.html.twig', array());
    }

    /**
     * Contact List
     *
     * @Route("/contact_list", name="contact_list")
     * @Method({"GET", "POST"})
     */
    public function contactListAction(Request $request){
        $report = array();
        $report['title'] = $this->getUser()->getActiveChannel()->getName() . ' Contact List';
        $report['headers'] = array(
            'Username',
            'Email',
            'Phone #',
            'Last Login',
            'Full Name',
            'Address',
            'City',
            'State',
            'Zip Code',
        );
        $report['data'] = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:User')->findUsersByChannel($this->getUser()->getActiveChannel());
        return $this->render('ReportBundle:Reports:contact-list.html.twig', array('report' => $report));
    }
}
