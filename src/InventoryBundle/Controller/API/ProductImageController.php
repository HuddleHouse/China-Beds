<?php

namespace InventoryBundle\Controller\API;

use InventoryBundle\Entity\ProductImage;
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
     * @Route("/api_get_all_product_images", name="api_get_all_product_images")
     */
    public function getImagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select * from product_images i where i.product_id = :id");
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
        $statement = $connection->prepare("delete from product_images where :id = id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            return $this->getImagesAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @param Request $request
     * @Route("/api_set_product_image_as_detail", name="api_set_product_image_as_detail")
     */
    public function setProductImageAsDetail(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        if ( $image = $em->getRepository('InventoryBundle:ProductImage')->find($id) ) {
            foreach($image->getProduct()->getImages() as $prod_image) {
                $prod_image->setDetailImage(false);
                $em->persist($prod_image);
            }
            $image->setDetailImage(true);
            $em->persist($image);

            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }
}
