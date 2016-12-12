<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 * @package AppBundle\Controller
 * @Route("/contact_us")
 */
class ContactController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="contact_us")
     * @Method({"GET"})
     */
    public function contactAction()
    {
        return $this->render('AppBundle:Contact:contact.html.twig');
    }

}