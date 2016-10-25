<?php

namespace WebsiteBundle\Controller;

use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

    public function warrantyIndexAction()
    {
        return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
            'site' => strtolower($this->getChannel()->getName())
        ));
    }

    public function contactIndexAction()
    {
        return $this->render('WebsiteBundle:Website:contact.html.twig', array(
            'site' => strtolower($this->getChannel()->getName())
        ));
    }

    public function  mattressIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();

        $mattresses = $em->getRepository('InventoryBundle:Product')->getAllMattressesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:mattresses.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'mattresses' => $mattresses
        ));
    }

    public function  singleMattressIndexAction(Product $product) {
        return $this->render('WebsiteBundle:Website:single-mattresses.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'product' => $product
        ));
    }

    public function  pillowsIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();
        $pillows = $em->getRepository('InventoryBundle:Product')->getAllPillowsForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:pillows.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'pillows' => $pillows
        ));
    }

    public function  adjustablesIndexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $channel = $this->getChannel();

        $adjustables = $em->getRepository('InventoryBundle:Product')->getAllAdjustablesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:adjustables.html.twig', array(
            'site' => strtolower($this->getChannel()->getName()),
            'adjustables' => $adjustables
        ));
    }
}
