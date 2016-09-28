<?php

namespace OrderBundle\Controller\API;

use Doctrine\DBAL\Types\DateType;
use OrderBundle\Services\LedgerService;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * Office controller.
 *
 * @Route("/api")
 */
class LedgerController extends Controller
{
    /**
     * Creates a new ledger entry.
     *
     * @Route("/api_new_ledger", name="api_new_ledger")
     * @Method({"GET", "POST"})
     */
    public function newLedgerAction(Request $request)
    {
        $service = new LedgerService($this->container);

        try {
            $ledger = $service->newEntry(
                $request->get('amount'),
                $request->get('submittedForUserId'),
                $this->getUser(),
                $request->get('achRequested'),
                $request->get('description'),
                $request->get('type'),
                $request->get('typeId')
            );
        } catch (\Exception $e) {
            return new JsonResponse("Error creating ledger entry: " . $e->getMessage(), 500);
        }

        return new JsonResponse($ledger);
    }
}

