<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Services\BaseService;
use InventoryBundle\Entity\Channel;
use InventoryBundle\Entity\WarrantyClaim;
use OrderBundle\Entity\Orders;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WarehouseBundle\Entity\PurchaseOrder;
use WarehouseBundle\Entity\Warehouse;

class EmailService extends BaseService
{


    /**
     * Send admin order notification
     *
     * @param Orders $order
     */
    public function sendAdminOrderNotification(Orders $order) {
        $body = $this->container->get('twig')->render(
            '@Order/Emails/admin-order-notification.html.twig',
            ['order' => $order]
        );

        $this->sendEmail([
            'subject'   => sprintf('%s Order #%s Received!', $order->getChannel()->getName(), $order->getOrderId()),
            'from'      => $order->getChannel()->getFromEmailAddress(),
            'to'        => $order->getChannel()->getSupportEmailAddress(),
            'body'      => $body
        ]);
    }

    public function sendCustomerOrderNotification(Orders $order) {
        $body = $this->container->get('twig')->render(
            '@Order/Emails/customer-order-notification.html.twig',
            ['order' => $order]
        );

        $this->sendEmail([
            'subject'   => sprintf('%s Order #%s Received!', $order->getChannel()->getName(), $order->getOrderId()),
            'from'      => $order->getChannel()->getFromEmailAddress(),
            'to'        => $order->getSubmittedForUser()->getEmail(),
            'body'      => $body
        ]);
    }


    public function sendWarehouseOrderNotification(Orders $order) {
        $em = $this->container->get('doctrine')->getManager();

        $warehouses = [];

        foreach($order->getProductVariants() as $product) {
            foreach($product->getWarehouseInfo() as $info) {
                if ( $info->getQuantity() > 0 ) {
                    $warehouses[$info->getWarehouse()->getId()][] = $info->getOrdersProductVariant();
                }
            }
        }

        foreach($warehouses as $warehouse_id => $products) {
            $warehouse = $em->getRepository('WarehouseBundle:Warehouse')->find($warehouse_id);
            echo "==== " . $warehouse->getName() . " ====\n";
//            foreach($warehouse->getManagers() as $manager) {
//                echo "      " . $manager->getDisplayName() . "\n";
                $files = [];
                foreach($products as $product) {
                    echo "      " . $product->getProductVariant()->getProduct()->getName() . ' ' . $product->getProductVariant()->getName() . "\n";
                    foreach ($product->getWarehouseInfo() as $info) {
                        foreach ($info->getShippingLabels() as $label) {
                            $files[] = $label->getAbsolutePath();
                        }
                    }
                }
                $body = $this->container->get('twig')->render(
                    '@Order/Emails/warehouse-order-notification.html.twig',
                    ['order' => $order, 'products' => $products, 'warehouse' => $warehouse]
                );
                $this->sendEmail(
                    [
                        'subject' => sprintf(
                            '%s Order #%s Ready %!',
                            $order->getIsPickUp() ? 'For Pick Up' : 'To Ship',
                            $order->getChannel()->getName(),
                            $order->getOrderId()
                        ),
                        'from'  => $order->getChannel()->getFromEmailAddress(),
                        'to'    => explode(',', $warehouse->getEmail()),
                        'body'  => $body,
                        'files' => $files
                    ]
                );
//            }
        }

//        print_r($warehouses); exit;
        exit;

        $warehouses = $em->getRepository('WarehouseBundle:Warehouse')->getWarehousesForOrder($order);

        foreach($warehouses as $warehouse) {
            echo "==== " . $warehouse->getName() . " ====\n";
            $products = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouse($order, $warehouse);
            echo "  " . count($products) . "\n";
            foreach($products as $product) {
                echo "      " . $product->getProductVariant()->getProduct()->getName() . ' ' . $product->getProductVariant()->getName() . "\n";
            }
            foreach($warehouse->getManagers() as $manager) {
                echo "      " . $manager->getDisplayName() . "\n";
                $files = [];
                foreach($products as $product) {
                    echo "      " . $product->getProductVariant()->getProduct()->getName() . ' ' . $product->getProductVariant()->getName() . "\n";
                    foreach ($product->getWarehouseInfo() as $info) {
                        foreach ($info->getShippingLabels() as $label) {
                            $files[] = $label->getAbsolutePath();
                        }
                    }
                }
                $body = $this->container->get('twig')->render(
                    '@Order/Emails/warehouse-order-notification.html.twig',
                    ['order' => $order, 'products' => $products, 'manager' => $manager]
                );
//                $this->sendEmail(
//                    [
//                        'subject' => sprintf(
//                            '%s Order #%s Ready %!',
//                            $order->getIsPickUp() ? 'For Pick Up' : 'To Ship',
//                            $order->getChannel()->getName(),
//                            $order->getOrderId()
//                        ),
//                        'from'  => $order->getChannel()->getFromEmailAddress(),
//                        'to'    => $manager->getEmail(),
//                        'body'  => $body,
//                        'files' => $files
//                    ]
//                );
            }
        }
    }







    public function sendEmail($data, $html = true)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody($data['body'], $html ? 'text/html' : 'text/plain')
            ->addBcc('jeremi.bergman@icloud.com');

        if ( isset($data['files']) && is_array($data['files']) ) {
            foreach($data['files'] as $filename) {
                $message->attach(\Swift_Attachment::fromPath($filename));
            }

        }

        if ( isset($data['cc']) ) {
            $message->addCc($data['cc']);
        }

        $this->mailer->send($message);
    }

    /**
     * @param Channel $channel
     * @param Orders $order
     * @param $orderReceipt
     */
    public function sendOrderReceipt(Channel $channel, Orders $order, $orderReceipt)
    {
        $settings = $this->container->get('settings_service');

        if ($settings->get('user-receipt') == 'yes') {
            $this->sendEmail(
                array(
                    'subject' => $channel->getName() . ' Order Receipt',
                    'from' => $channel->getEmailUrl(),
                    'to' => $order->getShipEmail(),
                    'body' => $orderReceipt
                )
            );
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
            ), false);
    }

    public function sendPortETAEmail(PurchaseOrder $po) {
        $settings = $this->container->get('settings_service');

        if($settings->get('warehouse-po-eta') == 'yes') {
            $url = $this->generateUrl('purchaseorder_show', array('id' => $po->getId()));
//        When the Port ETA is updated (later, after initial entry of PO)
//        Warehouse that will be receiving the stock receives email with full PO information.
//        should spool up an email to be sent at ETA
            $this->sendEmail(array(
                'subject' => 'Port ETA Updated',
                'from' => $po->getUser()->getUserChannels()->first()->getEmailUrl(),
                'to' => $po->getWarehouse()->getEmail(),
                'body' => 'Hello,\n\n'.
                    'You are receiving this email because your Purchase Order #' . $po->getOrderNumber() . ' is expected for to arrive on ' . $po->getStockDueDate()->format('m/d/y') .
                    '.\n\nFeel free to <a href="'.$url.'">click here</a> or paste the link below in your browser for details.\n\n' . $url
            ), false);
        }
    }

    public function sendETAUpdateEmail(PurchaseOrder $po) {
        $settings = $this->container->get('settings_service');
        if($settings->get('warehouse-po-reminder') == 'yes') {
            $url = $this->generateUrl('purchaseorder_show', array('id' => $po->getId()));
            $confirm_url = $this->generateUrl('api_purchase_order_receive_all', array());
//        On the Warehouse Eta date
//        Resend the same email with full PO information. This email should have contain a "confirm" or "Edit" link/button that the warehouse can use to quickly alert us that the stock has been received.
            $this->sendEmail(array(
                'subject' => 'Port ETA Updated',
                'from' => $po->getUser()->getUserChannels()->first()->getEmailUrl(),
                'to' => $po->getWarehouse()->getEmail(),
                'body' => 'Hello,\n\n'.
                    'You are receiving this email because your Purchase Order #' . $po->getOrderNumber() . ' is expected for to arrive today.' .
                    '.\n\nFeel free to <a href="'.$url.'">click here</a> or paste the link below in your browser for details.' .
                    '\n\n' . $url
            ), false);
        }
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}