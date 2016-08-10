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
class ProductImageController extends Controller
{
    /**
     * @Route("/add-product-image", name="api_add_product_image")
     */
    public function addProductImage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $request->files->get('file');
        $product_id = $request->request->get('product_id');

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $product_images_directory_path = $this->getParameter('product_images_directory');

        // Move the file to the directory where brochures are stored
        $file->move(
            $product_images_directory_path,
            $fileName
        );


        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_images (product_id, path) values (:product_id, :path)");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('path', $fileName);

        try {
            $statement->execute();
            return $this->getImagesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_remove_product_image", name="api_remove_product_image")
     */
    public function removeProductImage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select value, option_id, id from option_values where option_id = :option_id");
        $statement->bindValue('option_id', $id);


        try {
            $statement->execute();
            $data = $statement->fetchAll();
            return JsonResponse::create($data);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
