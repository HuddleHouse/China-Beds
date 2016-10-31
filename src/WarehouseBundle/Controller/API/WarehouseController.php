<?php

namespace WarehouseBundle\Controller\API;

use WarehouseBundle\Entity\PurchaseOrder;
use WarehouseBundle\Entity\PurchaseOrderProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Office controller.
 *
 * @Route("/api")
 */
class WarehouseController extends Controller
{

    /**
     * @Route("/api_get_all_warehouse_products", name="api_get_all_warehouse_products")
     */
    public function getAllProductsArray()
    {
        $em = $this->getDoctrine()->getManager();
        $products_all = $em->getRepository('InventoryBundle:Product')->findAll();
        $products = array();

        foreach($products_all as $prod) {
            $image_url = '/';
            foreach($prod->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            foreach($prod->getVariants() as $variant)
                $products[] = array(
                    'name' => $prod->getName().": ".$variant->getName(),
                    'id' => $variant->getId(),
                    'image_url' => $image_url
                );
        }

        return JsonResponse::create($products);
    }

    /**
     * @Route("/api_update_purchase_order_numbers", name="api_update_purchase_order_numbers")
     */
    public function updateDemNumbers(Request $request)
    {
        $physicalContainerNumber = $request->request->get('physicalContainerNumber');
        $factoryOrderNumber = $request->request->get('factoryOrderNumber');
        $poId = $request->request->get('purchaseOrderId');
        $em = $this->getDoctrine()->getManager();
        $po = $em->getRepository('WarehouseBundle:PurchaseOrder')->find($poId);
        $po->setFactoryOrderNumber($factoryOrderNumber);
        $po->setPhysicalContainerNumber($physicalContainerNumber);
        $em->persist($po);
        $em->flush();

        return JsonResponse::create(true);
    }

    /**
     * @Route("/api_get_warehouse_inventory_for_product", name="api_get_warehouse_inventory_for_product")
     */
    public function getWarehouseInventoryForProduct(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $warehouse_id = $request->request->get('warehouse_id');
        $product_variant_id = $request->request->get('product_variant_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT quantity from warehouse_inventory where product_variant_id = :product_variant_id and warehouse_id = :warehouse_id");
        $statement->bindValue('product_variant_id', $product_variant_id);
        $statement->bindValue('warehouse_id', $warehouse_id);
        $statement->execute();
        $quantity = $statement->fetch();

        if($quantity == false)
            return JsonResponse::create(0);
        else
            return JsonResponse::create($quantity['quantity']);
    }
}

