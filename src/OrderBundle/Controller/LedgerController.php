<?php

namespace OrderBundle\Controller;

use Doctrine\DBAL\Types\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OrderBundle\Entity\Ledger;
use OrderBundle\Form\CreditRequestType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Ledger controller.
 *
 * @Route("/ledger")
 */
class LedgerController extends Controller
{
    /**
     * Lists all Ledger entities if admin. Other roles can only see applicable ledgers
     *
     * @Route("/", name="ledger_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()->hasRole('ROLE_ADMIN'))
            $ledgers = $em->getRepository('OrderBundle:Ledger')->findAll();
        else
            $ledgers = $em->getRepository('OrderBundle:Ledger')->findby(array('user' => $this->getUser()));

        return $this->render('@Order/Ledger/index.html.twig', array(
            'ledgers' => $ledgers,
        ));
    }

    /**
     * Creates a new credit request.
     *
     * @Route("/new", name="ledger_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ledger = new Ledger();
        $form = $this->createForm('OrderBundle\Form\CreditRequestType', $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                if(!$ledger->getSubmittedForUser())
                    $ledger->setSubmittedForUser($this->getUser());
                $ledger->setSubmittedByUser($this->getUser());
                $ledger->setAchRequested(false);
                $ledger->setAmountCredited($ledger->getAmountRequested());
                $ledger->setDatePosted(new \DateTime());
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating credit request entry: ' . $e->getMessage());
                return $this->render('@Order/Ledger/new.html.twig', array(
                    'ledger' => $ledger,
                    'form' => $form->createView(),
                ));
            }

            $this->addFlash('notice', 'Credit request created.');
            return $this->redirectToRoute('ledger_edit', array('id' => $ledger->getId()));
        }

        return $this->render('@Order/Ledger/new.html.twig', array(
            'ledger' => $ledger,
            'form' => $form->createView()
        ));
    }

    /**
     * Shows admins credit requests so they can approve or deny them.
     *
     * @Route("/{id}", name="ledger_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Ledger $ledger)
    {
        $form = $this->createForm('OrderBundle\Form\CreditApprovalType', $ledger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $ledger->setCreditedByUser($this->getUser());
                $ledger->setDatePosted(new \DateTime());
                $ledger->setIsArchived(true);
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating credit request entry: ' . $e->getMessage());
                return $this->render('@Order/Ledger/show.html.twig', array(
                    'ledger' => $ledger,
                    'form' => $form->createView(),
                ));
            }

            $this->addFlash('notice', 'Credit posted.');
            return $this->redirectToRoute('ledger_index', array('id' => $ledger->getId()));
        }

        return $this->render('@Order/Ledger/show.html.twig', array(
            'ledger' => $ledger,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing credit request.
     *
     * @Route("/{id}/edit", name="ledger_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ledger $ledger)
    {
        $deleteForm = $this->createDeleteForm($ledger);
        $editForm = $this->createForm('OrderBundle\Form\CreditRequestType', $ledger);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($ledger);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating credit request entry: ' . $e->getMessage());
                return $this->render('@Order/Ledger/edit.html.twig', array(
                    'ledger' => $ledger,
                    'edit_form' => $editForm->createView(),
                ));
            }

            $this->addFlash('notice', 'Credit request updated.');
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
