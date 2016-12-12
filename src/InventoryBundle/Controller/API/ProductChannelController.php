<?php

namespace InventoryBundle\Controller\API;

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
class ProductChannelController extends Controller
{

    /**
     * @Route("/api_get_all_product_channel_checks", name="api_get_all_product_channel_checks")
     */
    public function getChannelsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select * from product_channels i where i.product_id = :id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            $data = $statement->fetchAll();
            return JsonResponse::create($data);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/toggle-product-channel", name="api_toggle_product_channel")
     */
    public function toggleProductChannel(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel_id = $request->request->get('id');
        $product_id = $request->request->get('product_id');

        //First check to see if the Product Channel entry exist, if so remove it.
        //If it doesn't exist then add it.
        $connection = $em->getConnection();
        $statement = $connection->prepare("select * from product_channels i where i.product_id = :product_id and i.channel_id = :channel_id");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('channel_id', $channel_id);

        try {
            $statement->execute();
            $data = $statement->fetch();
        }
        catch(\Exception $e){
            return JsonResponse::create(false);
        }

        if($data == false) {
            $statement = $connection->prepare("insert into product_channels (product_id, channel_id) values (:product_id, :channel_id)");
            $statement->bindValue('product_id', $product_id);
            $statement->bindValue('channel_id', $channel_id);

            try {
                $statement->execute();
                return $this->getChannelsAction($request);
            }
            catch(\Exception $e) {
                return JsonResponse::create(false);
            }
        }
        else {
            return $this->removeProductChannel($request);
        }


    }

    /**
     * @Route("/api_remove_product_channel", name="api_remove_product_channel")
     */
    public function removeProductChannel(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel_id = $request->request->get('id');
        $product_id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_channels where product_id = :product_id and channel_id = :channel_id");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('channel_id', $channel_id);

        try {
            $statement->execute();
            return $this->getChannelsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
