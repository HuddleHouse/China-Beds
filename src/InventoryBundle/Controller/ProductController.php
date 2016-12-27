<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\ProductChannel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\ProductImage;
use InventoryBundle\Entity\ProductSpecification;
use InventoryBundle\Entity\ProductAttribute;
use InventoryBundle\Entity\Attribute;
use InventoryBundle\Entity\Specification;
use InventoryBundle\Form\ProductType;
use QuickbooksBundle\Controller\ItemController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use InventoryBundle\Entity\ProductVariant;
/**
 * Product controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller
{
   /**
     * Used for export all products as json file.
     *
     * @Route("/export", name="admin_product_export")
     * @Method({"GET", "POST"})
     */
    public function exportAction(Request $request)
    {
	$em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        
        $productsArr = array();
        foreach ($products as $product) {  
		$productsArr[] = $product->toArray();
        }
        
        $response = new JsonResponse();
        $response->setData($productsArr);
        
        $now = date('YmdHis');
        $fileName = 'products-'.$now.'.json';
        $disposition = $response->headers->makeDisposition(
	    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
	    $fileName
	);

	$response->headers->set('Content-Disposition', $disposition);
	
	return $response;
    }
    
    /**
     * This route is used for testing product importer 
     *
     * @Route("/import", name="admin_product_import")
     * @Method({"GET", "POST"})
     */
    public function importAction(Request $request)
    {

	$products = json_decode(file_get_contents($this->get('kernel')->getRootDir() . '/../app/products/products-20161227184927.json'));
	
	$productImportService = $this->get('product_import_service');
	$productImportService->import($products,1,1);
	exit();
    }
    
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="admin_product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();

        $prod = [];
        foreach($products as $k => $product) {
            foreach($product->getChannels() as $product_channel) {
                if ( $product_channel->getChannel()->getId() == $this->getUser()->getActiveChannel()->getId() ) {
                    $prod[] = $product;
                }
            }
        }

        return $this->render('@Inventory/Product/index.html.twig', array(
            'products' => $prod,
        ));
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/new", name="admin_product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('InventoryBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $channel = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Channel')->find($this->getUser()->getActiveChannel()->getId());
                $pc = new ProductChannel();
                $pc->setChannel($channel);
                $product->addChannel($pc);
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                $this->addFlash('notice', 'Product Created successfully.');
                return $this->redirectToRoute('admin_product_edit', array('id' => $product->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Product ' . $e->getMessage());
                return $this->render('@Inventory/Product/new.html.twig', array(
                    'product' => $product,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="admin_product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('@Inventory/Product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="admin_product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('InventoryBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);


        $em = $this->getDoctrine()->getManager();

        $specs = $em->getRepository('InventoryBundle:Specification')->findAll();
        $cats = $em->getRepository('InventoryBundle:Category')->findAll();
        $channel = $this->getUser()->getActiveChannel()->getId();
        $image_attributes = $em->getRepository('InventoryBundle:Attribute')->findByChannels($channel);
        $channels = $em->getRepository('InventoryBundle:Channel')->findAll();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                $this->addFlash('notice', 'Product updated successfully.');
                return $this->redirectToRoute('admin_product_edit', array('id' => $product->getId())); 
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Product ' . $e->getMessage());
                return $this->render('@Inventory/Product/edit.html.twig', array(
                    'product' => $product,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'specs' => $specs,
                    'cats' => $cats,
                    'image_attributes' => $image_attributes,
                    'all_channels' => $channels,
                ));
            }
        }

        return $this->render('@Inventory/Product/edit.html.twig', array(
            'product' => $product,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'specs' => $specs,
            'cats' => $cats,
            'image_attributes' => $image_attributes,
            'all_channels' => $channels,
        ));
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="admin_product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($product);



                $em->flush();
                $this->addFlash('notice', 'Product deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'This product cannot be deleted. You can make it inactive if you no longer want it to show up on on the website or order forms.');
                return $this->redirectToRoute('admin_product_index');
            }
        }

        return $this->redirectToRoute('admin_product_index');
    }

    /**
     * Creates a form to delete a Product entity.
     *
     * @param Product $product The Product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}