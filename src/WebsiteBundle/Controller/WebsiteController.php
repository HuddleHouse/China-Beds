<?php

namespace WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WebsiteController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebsiteBundle:Website:home.html.twig', array(

        ));
    }
}
