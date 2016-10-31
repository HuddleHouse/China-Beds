<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Services\BaseService;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Entity\WarrantyClaim;
use OrderBundle\Entity\Orders;
use WarehouseBundle\Entity\Warehouse;

class EmailService extends BaseService
{
    public function sendEmail($data)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody($data['body'], 'text/html');

        $this->mailer->send($message);
    }

    /**
     * @param Channel $channel
     * @param Orders $order
     * @param $orderReceipt
     */
    public function sendOrderReceipt(Channel $channel, Orders $order, $orderReceipt) {
        $settings = $this->container->get('settings_service');

        if($settings->get('user-receipt') == 'yes') {
            $this->sendEmail(array(
                'subject' => $channel->getName() . ' Order Receipt',
                'from' => $channel->getEmailUrl(),
                'to' => $order->getShipEmail(),
                'body' => $orderReceipt
            ));
        }
    }

    /**
     * @param Channel $channel
     * @param Warehouse $warehouse
     * @param $orderReceipt
     */
    public function sendWarehouseOrderReceipt(Channel $channel, Warehouse $warehouse, $orderReceipt) {
        $settings = $this->container->get('settings_service');

        if($settings->get('warehouse-receipt') == 'yes' && $warehouse->getEmail() != null) {
            $this->sendEmail(array(
                'subject' => $channel->getName() . ' Order Receipt',
                'from' => $channel->getEmailUrl(),
                'to' => $warehouse->getEmail(),
                'body' => $orderReceipt
            ));
        }
    }

    /**
     * @param User $user
     * @param WarrantyClaim $warrantyClaim
     */
    public function sendWarrantyClaimAcknowledgementEmail(User $user, WarrantyClaim $warrantyClaim) {
        $settings = $this->container->get('settings_service');

        if($settings->get('warrantyclaim-acknowledgement') == 'yes')
            $this->sendEmail(array(
                'subject' => 'Warranty Claim Acknowledgement',
                'from' => $warrantyClaim->getChannel()->getEmailUrl(),
                'to' => $user->getEmail(),
                'body' => 'Dear '. $user->getFullName() .',\n\n'.
                    'Thank you for contacting ' . $warrantyClaim->getChannel()->getCompanyName() . '.' .
                    $settings->get('warrantyclaim-acknowledgement-text')
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