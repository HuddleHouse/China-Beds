<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Services\BaseService;
use InventoryBundle\Entity\WarrantyClaim;
use OrderBundle\Entity\Orders;

class EmailService extends BaseService
{
    public function sendEmail($data)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            //->setTo($data['to'])
            ->setTo('rajeffords@gmail.com')
            ->setBody($data['body'], 'text/html');

        $this->mailer->send($message);
    }

    public function sendOrderEmails(Orders $order) {
//        $warehouses = array();
//        foreach($order->getProductVariants() as $productVariant) {
//            foreach($productVariant->getWarehouseInfo() as $warehouseInfo) {
//                $warehouse = $warehouseInfo->getWarehouse();
//                $warehouses[$warehouse->getName()]['email'] = $warehouse->getEmail();
//            }

//        }
    }

    public function sendWarrantyClaimAcknowledgementEmail(User $user, WarrantyClaim $warrantyClaim) {
        $this->sendEmail(array(
            'subject' => '',
            'from' => '',
            'to' => '',
            'body' => 'Dear '. $user->getFullName() .',\n\n'.
                'Thank you for contacting ' . $warrantyClaim->getChannel()->getCompanyName() .
                '. Your warranty claim has been received and will be reviewed shortly. ' .
                'A customer service specialist may contact you for additional information as needed. ' .
                'You can expect a response with 3-7 business days. \n\n' .
                'Thank you for your patience and your patronage.'
        ));
    }

    public function sendPortETAEmail() {
//        When the Port ETA is updated (later, after initial entry of PO)
//        Warehouse that will be receiving the stock receives email with full PO information.
//        should spool up an email to be sent at ETA
    }

    public function sendETAUpdateEmail() {
//        On the Warehouse Eta date
//        Resend the same email with full PO information. This email should have contain a "confirm" or "Edit" link/button that the warehouse can use to quickly alert us that the stock has been received.
    }
}