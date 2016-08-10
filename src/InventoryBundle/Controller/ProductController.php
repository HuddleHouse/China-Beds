<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Product;
use InventoryBundle\Form\ProductType;
use QuickbooksBundle\Controller\ItemController;

/**
 * Product controller.
 *
 * @Route("/admin/product")
 */
class ProductController extends Controller
{
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

        return $this->render('@Inventory/Product/index.html.twig', array(
            'products' => $products,
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

        $productImageForm = $this->createForm('InventoryBundle\Form\ProductImageType');

        $em = $this->getDoctrine()->getManager();

        $specs = $em->getRepository('InventoryBundle:Specification')->findAll();
        $image_attributes = $em->getRepository('InventoryBundle:Attribute')->findAll();
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
                    'image_attributes' => $image_attributes,
                    'all_channels' => $channels,
                    'product_image_form' => $productImageForm->createView()
                ));
            }
        }

        return $this->render('@Inventory/Product/edit.html.twig', array(
            'product' => $product,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'specs' => $specs,
            'image_attributes' => $image_attributes,
            'all_channels' => $channels,
            'product_image_form' => $productImageForm->createView()
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
                $this->addFlash('error', 'Error deleting Product ' . $e->getMessage());
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
