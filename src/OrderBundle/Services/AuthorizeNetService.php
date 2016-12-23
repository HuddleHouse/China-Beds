<?php

namespace OrderBundle\Services;

use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use AppBundle\Entity\User;
use AppBundle\Services\BaseService;
use InventoryBundle\Entity\WarrantyClaim;
use OrderBundle\Entity\Orders;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthorizeNetService extends BaseService
{
    private $protected;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $data
     *
     * indexes should be
     *      amount = 00.00
     *      number = 4111111111111111
     *      expiry-month = 12
     *      expiry-year = 26
     *      cvv = 123
     *      order_id = 203943089038
     *
     * @return AnetAPI\AnetApiResponseType
     */
    function chargeCreditCard(array $data, $transaction_type = 'authCaptureTransaction'){

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->container->getParameter('authorize_login_id'));
        $merchantAuthentication->setTransactionKey($this->container->getParameter('authorize_tansaction_key'));
        $refId = 'ref' . time();
        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['number']);
        $creditCard->setExpirationDate($data['expiry-month'].$data['expiry-year']);
        $creditCard->setCardCode($data['cvv']);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($data['order_id']);
        $order->setDescription("Payment for Order #" . $data['order_id']);
        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType( $transaction_type );
        $transactionRequestType->setAmount($data['amount']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        if ( isset($data['trans_id']) ) {
            $transactionRequestType->setRefTransId($data['trans_id']);
        }

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        if ( $this->container->getParameter('authorize_test_mode') ) {
            $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
        } else {
            $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        }


        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == "Ok")
            {
                $tresponse = $response->getTransactionResponse();
                return [
                    'success' => true,
                    'auth_code' => $tresponse->getAuthCode(),
                    'trans_id'  => $tresponse->getTransId()
                ];
            }
            else
            {
                $tresponse = $response->getTransactionResponse();
                if($tresponse != null && $tresponse->getErrors() != null)
                {
                    $error_code = $tresponse->getErrors()[0]->getErrorCode();
                    $error_message = $tresponse->getErrors()[0]->getErrorText();
                }
                else
                {
                    $error_code = $response->getMessages()->getMessage()[0]->getCode();
                    $error_message = $response->getMessages()->getMessage()[0]->getText();
                }
                return [
                    'success' => false,
                    'error_code' => $error_code,
                    'error_message'  => $error_message
                ];
            }
        }
        else
        {
            return [
                'success' => false,
                'error_code' => -1,
                'error_message'  => 'No Response from Gateway'
            ];
        }
    }
}