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
     * Finds and displays a Channel entity.
     *
     * @Route("/switch/{id}", name="admin_channel_switch")
     * @Method("GET")
     */
    public function switchAction(Channel $channel)
    {
        $this->get('session')->set('active_channel', $channel);
        $this->getUser()->setActiveChannel($channel);
        return $this->redirectToRoute('fos_user_profile_show');
    }

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

                ///////////////////////////
                //logo uploads for Homepage
                //////////////////////////

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
                $channel->setFrontFooterOne($firstFooterName);
                $channel->setFrontFooterTwo($secondFooterName);
                $channel->setFrontFooterThree($thirdFooterName);

                /////////////////////////
                //FAQ page image uploads
                /////////////////////////

                //warranty upload
                $warrantyPic = $channel->getFaqWarrantyPic();
                $warrantyPicName = md5(uniqueid()).'.'.$warrantyPic->guessExtension();
                $warrantyPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $warrantyPicName
                );

                //unpacking upload
                $unpackingPic = $channel->getFaqUnpackingPic();
                $unpackingPicName = md5(uniqueid()).'.'.$unpackingPic->guessExtension();
                $unpackingPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $unpackingPicName
                );

                //support upload
                $supportPic = $channel->getFaqSupportPic();
                $supportPicName = md5(uniqueid()).'.'.$supportPic->guessExtension();
                $supportPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $supportPicName
                );

                //maintenance upload
                $maintenancePic = $channel->getFaqMaintenancePic();
                $maintenancePicName = md5(uniqueid()).'.'.$maintenancePic->guessExtension();
                $warrantyPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $maintenancePicName
                );

                //contact upload
                $contactPic = $channel->getFaqContactPic();
                $contactPicName = md5(uniqueid()).'.'.$contactPic->guessExtension();
                $contactPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $contactPicName
                );

                //terms & conditions upload
                $tandcPic = $channel->getFaqTCPic();
                $tandcPicName = md5(uniqueid()).'.'.$tandcPic->guessExtension();
                $tandcPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $tandcPicName
                );

                /////////////////////////////
                // Product Features Uploads
                ////////////////////////////

                //memory foam upload
                $memoryFoamPic = $channel->getPFmemoryFoamPic();
                $memoryFoamPicName = md5(uniqueid()).'.'.$memoryFoamPic->guessExtension();
                $memoryFoamPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $memoryFoamPicName
                );

                //side picture upload
                $sidePic = $channel->getPFSidePic();
                $sidePicName = md5(uniqueid()).'.'.$sidePic->guessExtension();
                $sidePic->move(
                    $this->getParameter('channel_upload_directory'),
                    $sidePicName
                );

                //renewable ersource upload
                $renewableResourcePic = $channel->getPFRenewResourcewPic();
                $renewableResourcePicName = md5(uniqueid()).'.'.$renewableResourcePic->guessExtension();
                $renewableResourcePic->move(
                    $this->getParameter('channel_upload_directory'),
                    $renewableResourcePicName
                );

                //semi-open cell structure upload
                $socsPic = $channel->getPFsocsPic();
                $socsPicName = md5(uniqueid()).'.'.$socsPic->guessExtension();
                $socsPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $socsPicName
                );

                //plant based oils picture
                $pboPic = $channel->getPFpboPic();
                $pboPicName = md5(uniqueid()).'.'.$pboPic->guessExtension();
                $memoryFoamPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $pboPicName
                );

                //bamboo charcoal upload
                $bCharcoalPic = $channel->getPFBCharcoalPic();
                $bCharcoalPicName = md5(uniqueid()).'.'.$bCharcoalPic->guessExtension();
                $bCharcoalPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $bCharcoalPicName
                );

                //bamboo fibers upload
                $bFiberPic = $channel->getPFBFibersPic();
                $bFiberPicName = md5(uniqueid()).'.'.$bFiberPic->guessExtension();
                $bFiberPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $bFiberPicName
                );

                //silk upload
                $silkPic = $channel->getPFSilkPic();
                $silkPicName = md5(uniqueid()).'.'.$silkPic->guessExtension();
                $silkPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $silkPicName
                );

                //aloe vera picture
                $aloeVeraPic = $channel->getPFAloeVeraPic();
                $aloeVeraPicName = md5(uniqueid()).'.'.$aloeVeraPic->guessExtension();
                $aloeVeraPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $aloeVeraPicName
                );


                //certified foam picture
                $certFoamPic = $channel->getPFCertifiedPic();
                $certFoamPicName = md5(uniqueid()).'.'.$certFoamPic->guessExtension();
                $certFoamPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $certFoamPicName
                );

                //OEKO TEX standard upload
                $oekotexPic = $channel->getPFTexStandPic();
                $oekotexPicName = md5(uniqueid()).'.'.$oekotexPic->guessExtension();
                $oekotexPic->move(
                    $this->getParameter('channel_upload_directory'),
                    $oekotexPicName
                );







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
