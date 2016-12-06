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
        $editForm = $this->createForm(RequestCreditType::class, $creditRequest);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credit_request_index');
        }

        return $this->render('OrderBundle:RequestCredit:edit.html.twig', array(
            'creditRequest' => $creditRequest,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a creditRequest entity.
     *
     * @Route("/{id}", name="credit_request_delete")
     * @Method({"POST", "GET"})
     */
    public function deleteAction(Request $request, CreditRequest $creditRequest)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($creditRequest);

            $em->flush();
            $this->addFlash('notice', 'Credit request deleted successfully');

            return $this->redirectToRoute('credit_request_index');
        }catch(\Exception $e){
            $this->addFlash('error', 'Credit request not deleted : ' . $e);
            return $this->redirectToRoute('credit_request_index');
        }

    }
}
