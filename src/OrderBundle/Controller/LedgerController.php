<?php

namespace OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OrderBundle\Entity\Ledger;
use OrderBundle\Form\LedgerType;

/**
 * Ledger controller.
 *
 * @Route("/ledger")
 */
class LedgerController extends Controller
{
    /**
     * Lists all Ledger entities.
     *
     * @Route("/", name="ledger_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ledgers = $em->getRepository('OrderBundle:Ledger')->findAll();

        return $this->render('@Order/Ledger/index.html.twig', array(
            'ledgers' => $ledgers,
        ));
    }

    /**
     * Creates a new Ledger entity.
     *
     * @Route("/new", name="ledger_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ledger = new Ledger();
        $form = $this->createForm('OrderBundle\Form\LedgerType', $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating ledger entry: ' . $e->getMessage());
                return $this->render('@Order/Ledger/new.html.twig', array(
                    'ledger' => $ledger,
                    'form' => $form->createView(),
                ));
            }

            $this->addFlash('notice', 'Ledger entry created.');
            return $this->redirectToRoute('ledger_show', array('id' => $ledger->getId()));
        }

        return $this->render('@Order/Ledger/new.html.twig', array(
            'ledger' => $ledger,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a Ledger entity.
     *
     * @Route("/{id}", name="ledger_show")
     * @Method("GET")
     */
    public function showAction(Ledger $ledger)
    {
        $deleteForm = $this->createDeleteForm($ledger);

        return $this->render('@Order/Ledger/show.html.twig', array(
            'ledger' => $ledger,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Ledger entity.
     *
     * @Route("/{id}/edit", name="ledger_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ledger $ledger)
    {
        $deleteForm = $this->createDeleteForm($ledger);
        $editForm = $this->createForm('OrderBundle\Form\LedgerType', $ledger);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating ledger entry: ' . $e->getMessage());
                return $this->render('@Order/Ledger/edit.html.twig', array(
                    'ledger' => $ledger,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

            $this->addFlash('notice', 'Ledger updated.');
            return $this->redirectToRoute('ledger_edit', array('id' => $ledger->getId()));
        }

        return $this->render('@Order/Ledger/edit.html.twig', array(
            'ledger' => $ledger,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Ledger entity.
     *
     * @Route("/{id}", name="ledger_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ledger $ledger)
    {
        $form = $this->createDeleteForm($ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($ledger);
                $em->flush();
                $this->addFlash('notice', 'Ledger removed.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error removing ledger entry: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('ledger_index');
    }

    /**
     * Creates a form to delete a Ledger entity.
     *
     * @param Ledger $ledger The Ledger entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ledger $ledger)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ledger_delete', array('id' => $ledger->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
