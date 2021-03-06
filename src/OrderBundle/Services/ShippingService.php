<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 12/29/16
 * Time: 9:33 AM
 */

namespace OrderBundle\Services;

use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersShippingLabel;
use RocketShipIt;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingService
{
    protected $container;

    /**
     * LedgerService constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function generateShippingLabelsForOrder(Orders &$orders, $flush = false)
    {
        $em = $this->container->get('doctrine')->getManager();

        $config = $this->getConfigForChannel($orders->getChannel());

        $numProdVariants = 0; //count($orders->getProductVariants());
        foreach ($orders->getProductVariants() as $variant) {
            $numProdVariants += $variant->getQuantity();
        }

        foreach ($orders->getShippingLabels() as $label) {
            $em->remove($label);
        }

        $em->persist($orders);

        $shipmentId = '';
        $count = 0;
        $charges = 0;
        foreach ($orders->getProductVariants() as $variant) {
            foreach ($variant->getWarehouseInfo() as $info) {
                for ($i = 0; $i < $info->getQuantity(); $i++) {
                    $count++;
                    $shipment = new \RocketShipIt\Shipment('fedex', ['config' => $config]);

                    $shipment->setParameter('toCompany', $orders->getShipName());
                    $shipment->setParameter('toName', $orders->getShipName());
                    $shipment->setParameter('toPhone', $orders->getShipPhone());
                    $shipment->setParameter('toAddr1', $orders->getShipAddress());
                    if ($orders->getShipAddress2() != '') {
                        $shipment->setParameter('toAddr2', $orders->getShipAddress2());
                    }
                    $shipment->setParameter('toCity', $orders->getShipCity());
                    $shipment->setParameter('toState', $orders->getState()->getAbbreviation());
                    $shipment->setParameter('toCode', $orders->getShipZip());

                    $shipment->setParameter('residentialAddressIndicator', '0');
                    $shipment->setParameter('service', 'FEDEX_GROUND');

                    /*
                     * THis needs to change once warehouses have addresses.
                     *
                     * They also need to add the fedex numbers of Distributors when applicable..
                     */
                    $shipment->setParameter('shipAddr1', $info->getWarehouse()->getAddress1());
                    $shipment->setParameter('shipCity', $info->getWarehouse()->getCity());
                    $shipment->setParameter('shipState', $info->getWarehouse()->getState()->getAbbreviation());
                    $shipment->setParameter('shipCode', $info->getWarehouse()->getZip());
                    $shipment->setParameter('shipPhone', $info->getWarehouse()->getPhone());

                    $shipment->setParameter('packageCount', $numProdVariants);
                    $shipment->setParameter('sequenceNumber', $count);

                    if ($count != 1) {
                        $shipment->setParameter('shipmentIdentification', $shipmentId);
                    }

                    $dimensions = explode('x', $variant->getProductVariant()->getFedexDimensions());
                    if (count($dimensions) == 1) {
                        $dimensions = explode('X', $variant->getProductVariant()->getFedexDimensions());
                    }

                    if (isset($dimensions[0])) {
                        $shipment->setParameter('length', $dimensions[0]);
                    }
                    if (isset($dimensions[1])) {
                        $shipment->setParameter('width', $dimensions[1]);
                    }
                    if (isset($dimensions[2])) {
                        $shipment->setParameter('height', $dimensions[2]);
                    }
                    $shipment->setParameter('weight', $variant->getProductVariant()->getWeight());


//                    if ($orders->getSubmittedForUser()->getDistributorFedexNumber(
//                        ) != null || $orders->getSubmittedForUser()->getDistributorFedexNumber() != ''
//                    ) {
//                        $shipment->setParameter('paymentType', 'THIRD_PARTY');
//                        $shipment->setParameter(
//                            'thirdPartyAccount',
//                            $orders->getSubmittedForUser()->getDistributorFedexNumber()
//                        );
//                    }

                    $response = $shipment->submitShipment();
//                    file_put_contents('/tmp/shipping.log', PHP_EOL . $shipment->debug(), FILE_APPEND);
//                    echo $shipment->debug();
                    if (isset($response['trk_main'])) {
                        if ($count == 1) {
                            $shipmentId = $response['trk_main'];
                        }
                    } else {
                        throw new \Exception($response['error']);

                    }

                    foreach ($response['pkgs'] as $pkg) {
                        $path = 'uploads/shipping/' . $pkg['pkg_trk_num'] . '.' . $pkg['label_fmt'];
                        file_put_contents(
                            $this->container->get('kernel')->getRootDir() . '/../web/' . $path,
                            base64_decode($pkg['label_img'])
                        );

                        $orderShippingLabel = new OrdersShippingLabel();
                        $orderShippingLabel->setPath($path);
                        $orderShippingLabel->setOrder($orders);
                        $orderShippingLabel->setTrackingNumber($pkg['pkg_trk_num']);
                        $info->addShippingLabel($orderShippingLabel);
                    }
//                    $em->persist($orders);

                    if ($count == $numProdVariants) {
//                        print_r($response); exit;
                        $charges = $response['charges'];
                        $orders->setEstimatedShipping($orders->getShipping());
                        $orders->setShipping($charges);
                    }

                }
            }
        }

        if ($flush) {
            $em->persist($orders);
            $em->flush();
        }

        return [
            'rate' => $charges
        ];
    }

    public function getConfigForChannel(Channel $channel)
    {
        $config = new RocketShipIt\Config();
        $config->setDefault('generic', 'debugMode', '0');
        $config->setDefault('fedex', 'key', $channel->getFedexKey());
        $config->setDefault('fedex', 'password', $channel->getFedexPassword());
        $config->setDefault('fedex', 'accountNumber', $channel->getFedexNumber());
        $config->setDefault('fedex', 'meterNumber', $channel->getFedexMeterNumber());

        return $config;
    }

    public function calculateShipping(Orders $order, $debug = false)
    {
        $config = $this->getConfigForChannel($order->getChannel());

        $rates = [];
        foreach ($order->getProductVariants() as $variant) {
//            file_put_contents('/tmp/shipping.log', PHP_EOL . "=== variant id " . get_class($variant) . " " . $variant->getId() . "\n", FILE_APPEND);
            foreach ($variant->getWarehouseInfo() as $info) {
//                file_put_contents('/tmp/shipping.log', PHP_EOL . "==== info id " . get_class($info) . " " . $info->getId() . "\n", FILE_APPEND);
                for ($i = 0; $i < $info->getQuantity(); $i++) {
//                    file_put_contents('/tmp/shipping.log', PHP_EOL . "===== qty " . $info->getQuantity() . "  i  " . $i . "\n", FILE_APPEND);
                    if (!isset($rates[$info->getWarehouse()->getId()])) {
                        $rate = new \RocketShipIt\Rate('fedex', ['config' => $config]);
                        $rate->setParameter('residentialAddressIndicator', '0');
                        $rate->setParameter('service', 'FEDEX_GROUND');

                        $rate->setParameter('toName', $order->getShipName());
                        $rate->setParameter('toPhone', $order->getShipPhone());
                        $rate->setParameter('toAddr1', $order->getShipAddress());
                        $rate->setParameter('toAddr2', $order->getShipAddress2());
                        $rate->setParameter('toCity', $order->getShipCity());
                        $rate->setParameter(
                            'toState',
                            $order->getState() ? $order->getState()->getAbbreviation() : null
                        );
                        $rate->setParameter('toCode', $order->getShipZip());

                        $rate->setParameter('shipContact', $info->getWarehouse()->getContact());
                        $rate->setParameter('shipPhone', $info->getWarehouse()->getPhone());
                        $rate->setParameter('shipAddr1', $info->getWarehouse()->getAddress1());
                        $rate->setParameter('shipCity', $info->getWarehouse()->getCity());
                        $rate->setParameter(
                            'shipState',
                            $info->getWarehouse()->getState() ? $info->getWarehouse()->getState()->getAbbreviation(
                            ) : null
                        );
                        $rate->setParameter('shipCode', $info->getWarehouse()->getZip());


                        $rates[$info->getWarehouse()->getId()] = $rate;
                    }


                    $dimensions = explode('x', $variant->getProductVariant()->getFedexDimensions());
                    if (count($dimensions) == 1) {
                        $dimensions = explode('X', $variant->getProductVariant()->getFedexDimensions());
                    }

                    $package = new \RocketShipIt\Package('fedex', ['config' => $config]);

                    $package->setParameter('shipContact', $info->getWarehouse()->getContact());
                    $package->setParameter('shipPhone', $info->getWarehouse()->getPhone());
                    $package->setParameter('shipAddr1', $info->getWarehouse()->getAddress1());
                    $package->setParameter('shipCity', $info->getWarehouse()->getCity());
                    $package->setParameter(
                        'shipState',
                        $info->getWarehouse()->getState() ? $info->getWarehouse()->getState()->getAbbreviation() : null
                    );
                    $package->setParameter('shipCode', $info->getWarehouse()->getZip());
                    $package->setParameter('length', "$dimensions[0]");
                    $package->setParameter('width', "$dimensions[1]");
                    $package->setParameter('height', "$dimensions[2]");
                    $package->setParameter('weight', $variant->getProductVariant()->getWeight());
                    $rates[$info->getWarehouse()->getId()]->addPackageToShipment($package);
                }
            }
        }

        $total_rate = 0;
        foreach ($rates as $warehouse_id => $rate) {
            $response = $rate->getSimpleRates();
//            file_put_contents('/tmp/shipping.log', PHP_EOL . $rate->debug(), FILE_APPEND);
//            file_put_contents('/tmp/shipping.log', PHP_EOL . $rate->packageCount, FILE_APPEND);


            $data = array_pop($response);

            if (!isset($data['rate'])) {
                mail('jeremib@gmail.com', 'Error calc shipping', $rate->debug());
                $data = array();
                $data['rate'] = 0;
                $data['service_code'] = 'FEDEX_GROUND';
                $data['desc'] = 'FedEx Ground';
                $total_rate += 0;
            } else {
                $total_rate += $data['rate'];
            }
        }

        return [
            'rate' => $total_rate,
            'service_code' => 'FEDEX_GROUND',
            'desc' => 'FedEx Ground'
        ];

    }
}