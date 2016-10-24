<?php

namespace WebsiteBundle\Controller;

use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WebsiteController extends Controller
{

    public function indexAction($site)
    {
        $em = $this->getDoctrine()->getManager();
        if($site == 'mlily'){
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'MLILY'));
        }
        else{
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'BedBoss'));
        }
        return $this->render('WebsiteBundle:Website:home.html.twig', array(
            'site' => $site,
            'channel' => $channel
        ));
    }

    public function faqIndexAction($site)
    {
        return $this->render('WebsiteBundle:Website:faq.html.twig', array(
            'site' => $site
        ));
    }

    public function productFeaturesIndexAction($site)
    {
        return $this->render('WebsiteBundle:Website:product-features.html.twig', array(
            'site' => $site
        ));
    }

    public function retailerIndexAction($site)
    {
        return $this->render('WebsiteBundle:Website:retailer.html.twig', array(
            'site' => $site
        ));
    }

    public function warrantyIndexAction($site)
    {
        return $this->render('WebsiteBundle:Website:warranty.html.twig', array(
            'site' => $site
        ));
    }

    public function contactIndexAction($site)
    {
        return $this->render('WebsiteBundle:Website:contact.html.twig', array(
            'site' => $site
        ));
    }

    public function  mattressIndexAction($site) {
        $em = $this->getDoctrine()->getEntityManager();

        if($site == 'mlily')
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'MLILY'));
        else
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'BedBoss'));

        $mattresses = $em->getRepository('InventoryBundle:Product')->getAllMattressesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:mattresses.html.twig', array(
            'site' => $site,
            'mattresses' => $mattresses
        ));
    }

    public function  singleMattressIndexAction($site, Product $product) {
        return $this->render('WebsiteBundle:Website:single-mattresses.html.twig', array(
            'site' => $site,
            'product' => $product
        ));
    }

    public function  pillowsIndexAction($site) {
        $em = $this->getDoctrine()->getEntityManager();

        if($site == 'mlily')
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'MLILY'));
        else
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'BedBoss'));

        $pillows = $em->getRepository('InventoryBundle:Product')->getAllPillowsForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:pillows.html.twig', array(
            'site' => $site,
            'pillows' => $pillows
        ));
    }

    public function  adjustablesIndexAction($site) {
        $em = $this->getDoctrine()->getEntityManager();

        if($site == 'mlily')
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'MLILY'));
        else
            $channel = $em->getRepository('InventoryBundle:Channel')->findOneBy(array('name' => 'BedBoss'));

        $adjustables = $em->getRepository('InventoryBundle:Product')->getAllAdjustablesForChannelArray($channel);

        return $this->render('WebsiteBundle:Website:adjustables.html.twig', array(
            'site' => $site,
            'adjustables' => $adjustables
        ));
    }
}
