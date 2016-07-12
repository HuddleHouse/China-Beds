<?php

namespace InventoryBundle\Controller;

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
                $this->addFlash('notice', 'Promo Kit created successfully.');

                return $this->redirectToRoute('promokit_index', array('id' => $promoKit->getId()));
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
     * Finds and displays a PromoKit entity.
     *
     * @Route("/{id}", name="promokit_show")
     * @Method("GET")
     */
    public function showAction(PromoKit $promoKit)
    {
        $deleteForm = $this->createDeleteForm($promoKit);

        return $this->render('@Inventory/Promokit/show.html.twig', array(
            'promoKit' => $promoKit,
            'delete_form' => $deleteForm->createView(),
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
        $deleteForm = $this->createDeleteForm($promoKit);
        $editForm = $this->createForm('InventoryBundle\Form\PromoKitType', $promoKit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($promoKit);
                $em->flush();
                $this->addFlash('notice', 'Promo Kit updated successfully.');

                return $this->redirectToRoute('promokit_edit', array('id' => $promoKit->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error editing Promo Kit Item: ' . $e->getMessage());

                return $this->render('@Inventory/Promokit/edit.html.twig', array(
                    'promoKit' => $promoKit,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }


        }

        return $this->render('@Inventory/Promokit/edit.html.twig', array(
            'promoKit' => $promoKit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
