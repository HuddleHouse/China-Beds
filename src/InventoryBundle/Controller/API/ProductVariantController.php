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
class ProductVariantController extends Controller
{

    /**
     * @Route("/api_get_all_product_variants", name="api_get_all_product_variants")
     */
    public function getVariantsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('product_id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select * from product_variant i where i.product_id = :id");
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
     * @Route("/add-product-variant", name="api_add_product_variant")
     */
    public function addProductVariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $request->files->get('file');
        $product_id = $request->request->get('product_id');

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $product_variants_directory_path = $this->getParameter('product_variants_directory');

        // Move the file to the directory where brochures are stored
        $file->move(
            $product_variants_directory_path,
            $fileName
        );


        $connection = $em->getConnection();
        $statement = $connection->prepare("insert into product_variants (product_id, path) values (:product_id, :path)");
        $statement->bindValue('product_id', $product_id);
        $statement->bindValue('path', $fileName);

        try {
            $statement->execute();
            return $this->getvariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }

    /**
     * @Route("/api_remove_product_variant", name="api_remove_product_variant")
     */
    public function removeProductvariant(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $connection = $em->getConnection();
        $statement = $connection->prepare("delete from product_variants where :id = id");
        $statement->bindValue('id', $id);

        try {
            $statement->execute();
            return $this->getvariantsAction($request);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }
    }
}
