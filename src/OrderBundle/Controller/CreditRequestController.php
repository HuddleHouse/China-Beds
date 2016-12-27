<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\CreditRequest;
use OrderBundle\Entity\Ledger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use OrderBundle\Form\RequestCreditType;

/**
 * Creditrequest controller.
 *
 * @Route("/credit/requests")
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

                $this->addFlash('notice', 'Successfully requested credit request');

                if ( $this->getUser()->hasRole('ROLE_ADMIN') ) {
                    return $this->redirectToRoute('credit_request_edit', ['id' => $creditRequest->getId()]);
                } else {
                    return $this->redirectToRoute('credit_request_index');
                }

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
        if ( $creditRequest->getIsApproved() ) {
            return $this->redirectToRoute('credit_request_index');
        }
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

    /**
     * @param CreditRequest $creditRequest
     * @Route("/approve/{id}", name="credit_request_approve")
     */
    public function approveCreditRequestAction(CreditRequest $creditRequest) {
        if ( $creditRequest->getIsApproved() ) {
            $this->addFlash('error', 'Credit request was already approved.');
            return $this->redirectToRoute('credit_request_index');
        }

        $creditRequest->setIsApproved(true);

        $ledger = new Ledger();
        $ledger->setAmountRequested($creditRequest->getRequestAmount());
        $ledger->setAmountCredited($creditRequest->getRequestAmount());
        $ledger->setDescription('Created from Credit Request #' . $creditRequest->getId());
        $ledger->setType(Ledger::TYPE_CREDIT);
        $ledger->setDatePosted(new \DateTime());
        $ledger->setDateCreated(new \DateTime());
        $ledger->setSubmittedByUser($creditRequest->getSubmittedByUser());
        $ledger->setSubmittedForUser($creditRequest->getSubmittedForUser());
        $ledger->setCreditRequest($creditRequest);
        $ledger->setChannel($creditRequest->getChannel());
        $ledger->setIsApproved(true);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($creditRequest);
            $em->persist($ledger);

            $em->flush();

            $this->addFlash('notice', 'Credit request approved and ledger entry added.');
            return $this->redirectToRoute('credit_request_index');

        }catch(\Exception $e){
            $this->addFlash('error', 'Credit request not be approved : ' . $e);
            return $this->redirectToRoute('credit_request_index');
        }

    }
}
