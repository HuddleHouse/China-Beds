<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Form\ChannelType;

/**
 * Channel controller.
 *
 * @Route("/admin/channel")
 */
class ChannelController extends Controller
{
    /**
     * Lists all Channel entities.
     *
     * @Route("/", name="admin_channel_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $channels = $em->getRepository('InventoryBundle:Channel')->findAll();

        return $this->render('@Inventory/Channel/index.html.twig', array(
            'channels' => $channels,
        ));
    }

    /**
     * Creates a new Channel entity.
     *
     * @Route("/new", name="admin_channel_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $channel = new Channel();
        $form = $this->createForm('InventoryBundle\Form\ChannelType', $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($channel);
                $em->flush();

                $this->addFlash('notice', 'Channel created successfully.');

                return $this->redirectToRoute('admin_channel_show', array('id' => $channel->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Channel: ' . $e->getMessage());

                return $this->render('@Inventory/Channel/new.html.twig', array(
                    'channel' => $channel,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Channel/new.html.twig', array(
            'channel' => $channel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Channel entity.
     *
     * @Route("/{id}", name="admin_channel_show")
     * @Method("GET")
     */
    public function showAction(Channel $channel)
    {
        $deleteForm = $this->createDeleteForm($channel);

        return $this->render('@Inventory/Channel/show.html.twig', array(
            'channel' => $channel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Channel entity.
     *
     * @Route("/{id}/edit", name="admin_channel_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Channel $channel)
    {
        $deleteForm = $this->createDeleteForm($channel);
        $editForm = $this->createForm('InventoryBundle\Form\ChannelType', $channel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();


                //start edit

                //front logo upload
                $frontLogo = $channel->getFrontLogo();
                $frontLogoName = md5(uniqid()).'.'.$frontLogo->guessExtension();
                $frontLogo->move(
                    $this->getParameter('channel_upload_directory'),
                    $frontLogoName
                );

                //first slider upload
                $firstSlider = $channel->getFrontSliderOne();
                $firstSliderName = md5(uniqid()).'.'.$firstSlider->guessExtension();
                $firstSlider->move(
                    $this->getParameter('channel_upload_directory'),
                    $firstSliderName
                );

                //second slider upload
                $secondSlider = $channel->getFrontSliderTwo();
                $secondSliderName = md5(uniqid()).'.'.$secondSlider->guessExtension();
                $secondSlider->move(
                    $this->getParameter('channel_upload_directory'),
                    $secondSliderName
                );

                //third slider upload
                $thirdSlider = $channel->getFrontSliderThree();
                $thirdSliderName = md5(uniqid()).'.'.$thirdSlider->guessExtension();
                $thirdSlider->move(
                    $this->getParameter('channel_upload_directory'),
                    $thirdSliderName
                );

                //first footer box upload
                $firstFooter = $channel->getFrontFooterOne();
                $firstFooterName = md5(uniqid()).'.'.$firstFooter->guessExtension();
                $firstFooter->move(
                    $this->getParameter('channel_upload_directory'),
                    $firstFooterName
                );

                //second footer box upload
                $secondFooter = $channel->getFrontFooterTwo();
                $secondFooterName = md5(uniqid()).'.'.$secondFooter->guessExtension();
                $secondFooter->move(
                    $this->getParameter('channel_upload_directory'),
                    $secondFooterName
                );

                //third footer box upload
                $thirdFooter = $channel->getFrontFooterThree();
                $thirdFooterName = md5(uniqid()).'.'.$thirdFooter->guessExtension();
                $thirdFooter->move(
                    $this->getParameter('channel_upload_directory'),
                    $thirdFooterName
                );



                $channel->setFrontLogo($frontLogoName);
                $channel->setFrontSliderOne($firstSliderName);
                $channel->setFrontSliderTwo($secondSliderName);
                $channel->setFrontSliderThree($thirdSliderName);


                //end edit

                $em->persist($channel);
                $em->flush();

                $this->addFlash('notice', 'Channel updated successfully.');

                return $this->redirectToRoute('admin_channel_edit', array('id' => $channel->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Channel: ' . $e->getMessage());

                return $this->render('@Inventory/Channel/edit.html.twig', array(
                    'channel' => $channel,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Channel/edit.html.twig', array(
            'channel' => $channel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Channel entity.
     *
     * @Route("/{id}", name="admin_channel_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Channel $channel)
    {
        $form = $this->createDeleteForm($channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($channel);
                $em->flush();

                $this->addFlash('notice', 'Channel deleted successfully.');

            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Channel: ' . $e->getMessage());

                return $this->redirectToRoute('admin_channel_index');
            }
        }

        return $this->redirectToRoute('admin_channel_index');
    }

    /**
     * Creates a form to delete a Channel entity.
     *
     * @param Channel $channel The Channel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Channel $channel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_channel_delete', array('id' => $channel->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
