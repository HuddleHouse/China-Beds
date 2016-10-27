<?php

namespace OrderBundle\Services;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use AppBundle\Entity\User;
use AppBundle\Services\BaseService;
use InventoryBundle\Entity\WarrantyClaim;
use OrderBundle\Entity\Orders;

class AuthorizeNetService extends BaseService
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $data
     *
     * indexes should be
     *      amount = 00.00
     *      card_number = 4111111111111111
     *      expiration = 1226
     *      code = 123
     *
     * @return AnetAPI\AnetApiResponseType
     */
    function chargeCreditCard(array $data){

        $sandboxUrl = "https://apitest.authorize.net/xml/v1/request.api";

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->container->getParameter('default_sales_rep'));
        $merchantAuthentication->setTransactionKey($this->container->getParameter('default_sales_rep'));
        $refId = 'ref' . time();
        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['card_number']);
        $creditCard->setExpirationDate($data['expiration']);
        $creditCard->setCardCode($data['code']);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        $order = new AnetAPI\OrderType();
        $order->setDescription("New Item");
        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType( "authCaptureTransaction");
        $transactionRequestType->setAmount($data['amount']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($sandboxUrl);

        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == "Ok")
            {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null)
                {
                    echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                    echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                    echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
                    echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n";
                    echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
                }
                else
                {
                    echo "Transaction Failed \n";
                    if($tresponse->getErrors() != null)
                    {
                        echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }
                }
            }
            else
            {
                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();
                if($tresponse != null && $tresponse->getErrors() != null)
                {
                    echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                }
                else
                {
                    echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                }
            }
        }
        else
        {
            echo  "No response returned \n";
        }
        return $response;
    }
}