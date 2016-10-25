<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Setting controller.
 *
 * @Route("settings")
 */
class SettingsController extends Controller
{
    /**
     * Lists all setting entities.
     *
     * @Route("/", name="settings_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sections = $em->getRepository('AppBundle:SettingSection')->findAll();

        return $this->render('@App/Settings/index.html.twig', array(
            'sections' => $sections,
        ));
    }

    /**
     * Creates a new setting entity.
     *
     * @Route("/new", name="settings_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $setting = new Settings();
        $form = $this->createForm('AppBundle\Form\SettingsType', $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirectToRoute('settings_new');
        }

        return $this->render('@App/Settings/new.html.twig', array(
            'setting' => $setting,
            'form' => $form->createView(),
        ));
    }
}
