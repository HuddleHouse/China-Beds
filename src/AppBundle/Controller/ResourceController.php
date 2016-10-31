<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Resource controller.
 *
 * @Route("resources")
 */
class ResourceController extends Controller
{
    /**
     * Lists all resource entities.
     *
     * @Route("/", name="resource_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $resources = $em->getRepository('AppBundle:Resource')->findAll();

        return $this->render('AppBundle:Resource:index.html.twig', array(
            'resources' => $resources,
        ));
    }
}
