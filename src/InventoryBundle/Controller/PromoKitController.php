<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\PromoKitOrders;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\PromoKit;
use InventoryBundle\Form\PromoKitType;

/**
 * PromoKit controller.
 *
 * @Route("/promokit")
 */
class PromoKitController extends Controller
{
    /**
     * Lists all PromoKit entities.
     *
     * @Route("/", name="promokit_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $promoKits = $em->getRepository('InventoryBundle:PromoKit')->findAll();

        return $this->render('@Inventory/Promokit/index.html.twig', array(
            'promoKits' => $promoKits,
        ));
    }

    /**
     * Lists all PromoKitOrder entities.
     *
     * @Route("/orders", name="promokit_orders_index")
     * @Method("GET")
     */
    public function ordersIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()->hasRole('ROLE_ADMIN'))
            $promoKitOrders = $em->getRepository('InventoryBundle:PromoKitOrders')->findAll();
        else
            $promoKitOrders = $em->getRepository('InventoryBundle:PromoKitOrders')->findBy(array('submittedByUser' => $this->getUser()));

        return $this->render('@Inventory/Promokit/order-index.html.twig', array(
            'promoKitOrders' => $promoKitOrders,
        ));
    }

    /**
     * Creates a new PromoKit entity.
     *
     * @Route("/new", name="promokit_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $promoKit = new PromoKit();
        $form = $this->createForm('InventoryBundle\Form\PromoKitType', $promoKit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($promoKit);
                $em->flush();
                $this->addFlash('notice', 'Promo Kit Item created successfully.');

                return $this->redirectToRoute('promokit_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Promo Kit Item: ' . $e->getMessage());

                return $this->render('@Inventory/Promokit/new.html.twig', array(
                    'promoKit' => $promoKit,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('@Inventory/Promokit/new.html.twig', array(
            'promoKit' => $promoKit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new PromoKitOrders entity.
     *
     * @Route("/new_order", name="promokit_new_order")
     * @Method({"GET", "POST"})
     */
    public function newOrderAction(Request $request)
    {
        $promoKitOrder = new PromoKitOrders();
        $form = $this->createForm('InventoryBundle\Form\PromoKitOrderType', $promoKitOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $promoKitOrder->setSubmittedByUser($this->getUser());
                $this->getUser()->getPromoKitOrders()->add($promoKitOrder);
                $em->persist($promoKitOrder);
                $em->flush();
                $this->addFlash('notice', 'Promo Kit Order successfully submitted.');

                return $this->redirectToRoute('promokit_orders_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Promo Kit Order: ' . $e->getMessage());

                return $this->render('@Inventory/Promokit/new-order.html.twig', array(
                    'promoKitOrder' => $promoKitOrder,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('@Inventory/Promokit/new-order.html.twig', array(
            'promoKitOrder' => $promoKitOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit a new PromoKitOrders entity.
     *
     * @Route("/{id}/edit_order", name="promokit_edit_order")
     * @Method({"GET", "POST"})
     */
    public function editOrderAction(Request $request, PromoKitOrders $promoKitOrder)
    {
        $form = $this->createForm('InventoryBundle\Form\PromoKitOrderType', $promoKitOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($promoKitOrder);
                $em->flush();
                $this->addFlash('notice', 'Promo Kit Order successfully submitted.');

                return $this->redirectToRoute('promokit_orders_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Promo Kit Order: ' . $e->getMessage());

                return $this->render('@Inventory/Promokit/edit-order.html.twig', array(
                    'promoKitOrder' => $promoKitOrder,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('@Inventory/Promokit/edit-order.html.twig', array(
            'promoKitOrder' => $promoKitOrder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PromoKit entity.
     *
     * @Route("/{id}/edit", name="promokit_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PromoKit $promoKit)
    {
        $editForm = $this->createForm('InventoryBundle\Form\PromoKitType', $promoKit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($promoKit);
                $em->flush();
                $this->addFlash('notice', 'Promo Kit Item updated successfully.');

                return $this->redirectToRoute('promokit_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error editing Promo Kit Item: ' . $e->getMessage());

                return $this->render('@Inventory/Promokit/edit.html.twig', array(
                    'promoKit' => $promoKit,
                    'edit_form' => $editForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Promokit/edit.html.twig', array(
            'promoKit' => $promoKit,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a PromoKit entity.
     *
     * @Route("/{id}", name="promokit_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PromoKit $promoKit)
    {
        $form = $this->createDeleteForm($promoKit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($promoKit);
                $em->flush();

                $this->addFlash('notice', 'Promo Kit deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Promo Kit Item: ' . $e->getMessage());

                return $this->redirectToRoute('promokit_index');
            }
        }
        return $this->redirectToRoute('promokit_index');
    }

    /**
     * Creates a form to delete a PromoKit entity.
     *
     * @param PromoKit $promoKit The PromoKit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PromoKit $promoKit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('promokit_delete', array('id' => $promoKit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
