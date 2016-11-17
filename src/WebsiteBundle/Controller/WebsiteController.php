<?php

namespace WebsiteBundle\Controller;

use InventoryBundle\Entity\FrontWarrantyClaim;
use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class WebsiteController extends BaseController
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('WebsiteBundle:Website:home.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }

    public function faqIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('WebsiteBundle:Website:faq.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }

    public function productFeaturesIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('WebsiteBundle:Website:product-features.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }

    public function retailerIndexAction()
    {

        return $this->render('WebsiteBundle:Website:retailer.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }


    public function warrantyIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $warranty = new FrontWarrantyClaim();

        $form = $this->createForm('InventoryBundle\Form\FrontWarrantyClaimType', $warranty);
        $form->handleRequest($request);



        if($form->isSubmitted()) {
            $errors = $form->getErrors(true);
            if (0 !== count($errors)) {
                $error_msg = 'The following errors have been encountered:\n';
                foreach ($errors as $e) {
                    $error_msg .= $e->getMessage() . '\n';
                }
                return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
                    'site' => strtolower($this->getChannel()->getName()),
                    'channel' => $this->getChannel(),
                    'form' => $form->createView(),
                    'submitted' => array(false, $error_msg)
                ));
            } elseif ($form->isValid()) {
                //$warranty->setAddress($warranty->getAddress() . ' ' . $warranty->getCity() . ' ' . $warranty->getState() . ' ' . $warranty->getZip());
                try {
                    $warranty->uploadLawCopy();
                    $warranty->uploadReceipt();
                    $em->persist($warranty);
                    $em->flush();
                    return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
                        'site' => strtolower($this->getChannel()->getName()),
                        'channel' => $this->getChannel(),
                        'form' => $form->createView(),
                        'submitted' => array(true, '')
                    ));
                } catch (Exception $e) {
                    return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
                        'site' => strtolower($this->getChannel()->getName()),
                        'channel' => $this->getChannel(),
                        'form' => $form->createView(),
                        'submitted' => array(false, $e)
                    ));
                }

            }
        }

        return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel(),
            'form' => $form->createView(),
        ));
    }

    public function termsIndexAction()
    {
        return $this->render('WebsiteBundle:Website:terms.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }

    public function contactIndexAction()
    {
        return $this->render('WebsiteBundle:Website:contact.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'channel' => $this->getChannel()
        ));
    }

    public function  mattressIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();

        $mattresses = $em->getRepository('InventoryBundle:Product')->getAllMattressesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:mattresses.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'mattresses' => $mattresses,
            'channel' => $this->getChannel()
        ));
    }

    public function  singleMattressIndexAction(Product $product) {
        return $this->render('WebsiteBundle:Website:single-mattresses.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'product' => $product,
            'channel' => $this->getChannel()
        ));
    }

    public function  pillowsIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();
        $pillows = $em->getRepository('InventoryBundle:Product')->getAllPillowsForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:pillows.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'pillows' => $pillows,
            'channel' => $this->getChannel()
        ));
    }

    public function  adjustablesIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();

        $adjustables = $em->getRepository('InventoryBundle:Product')->getAllAdjustablesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:adjustables.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'adjustables' => $adjustables,
            'channel' => $this->getChannel()
        ));
    }

    public function fixFedexAction($filename) {
        return $this->redirect(sprintf('https://order.mlilyusa.com/model/fedex_label/img/%s', $filename));
    }
}
