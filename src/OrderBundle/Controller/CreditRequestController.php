<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\CreditRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use OrderBundle\Form\RequestCreditType;

/**
 * Creditrequest controller.
 *
 * @Route("/creditrequest")
 */
class CreditRequestController extends Controller
{
    /**
     * Lists all creditRequest entities.
     *
     * @Route("/", name="credit_request_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $creditRequests = $em->getRepository('OrderBundle:CreditRequest')->findAll();

        return $this->render('OrderBundle:RequestCredit:index.html.twig', array(
            'creditRequests' => $creditRequests,
        ));
    }

    /**
     * Creates a new creditRequest entity.
     *
     * @Route("/new", name="credit_request_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $creditRequest = new CreditRequest();
        $form = $this->createForm(RequestCreditType::class, $creditRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $user = $this->getUser();
                $channel =  $em->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());

                $creditRequest->setSubmittedByUser($user);
                $user->getCreditRequestBy()->add($creditRequest);
                $creditRequest->setChannel($channel);
                $channel->getCreditRequest()->add($creditRequest);

                $em->persist($creditRequest);
                $em->persist($channel);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('credit_request_index');
            }catch(\Exception $e){
                $this->addFlash('notice', 'Error creating credit request: ' . $e);

                return $this->render('OrderBundle:RequestCredit:new.html.twig', array(
                    'creditRequest' => $creditRequest,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('OrderBundle:RequestCredit:new.html.twig', array(
            'creditRequest' => $creditRequest,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing creditRequest entity.
     *
     * @Route("/{id}/edit", name="credit_request_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CreditRequest $creditRequest)
    {
        $deleteForm = $this->createDeleteForm($creditRequest);
        $editForm = $this->createForm(RequestCreditType::class, $creditRequest);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credit_request_index');
        }

        return $this->render('OrderBundle:RequestCredit:edit.html.twig', array(
            'creditRequest' => $creditRequest,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a creditRequest entity.
     *
     * @Route("/{id}", name="credit_request_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CreditRequest $creditRequest)
    {
        $form = $this->createDeleteForm($creditRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($creditRequest);
            $em->flush();
        }

        return $this->redirectToRoute('credit_request_index');
    }

    /**
     * Creates a form to delete a creditRequest entity.
     *
     * @param CreditRequest $creditRequest The creditRequest entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CreditRequest $creditRequest)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('credit_request_delete', array('id' => $creditRequest->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
