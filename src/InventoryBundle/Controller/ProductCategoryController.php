<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\ProductCategory;
use InventoryBundle\Form\ProductCategoryType;

/**
 * ProductCategory controller.
 *
 * @Route("/product/category")
 */
class ProductCategoryController extends Controller
{
    /**
     * Lists all ProductCategory entities.
     *
     * @Route("/", name="product_category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productCategories = $em->getRepository('InventoryBundle:ProductCategory')->findAll();

        return $this->render('@Inventory/ProductCategory/index.html.twig', array(
            'productCategories' => $productCategories,
        ));
    }

    /**
     * Creates a new ProductCategory entity.
     *
     * @Route("/new", name="product_category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm('InventoryBundle\Form\ProductCategoryType', $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCategory);
                $em->flush();

                $this->addFlash('notice', 'Product Category created successfully.');
                return $this->redirectToRoute('product_category_edit', array('id' => $productCategory->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Product Category ' . $e->getMessage());

                return $this->render('@Inventory/ProductCategory/new.html.twig', array(
                    'productCategory' => $productCategory,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/ProductCategory/new.html.twig', array(
            'productCategory' => $productCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProductCategory entity.
     *
     * @Route("/{id}", name="product_category_show")
     * @Method("GET")
     */
    public function showAction(ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);

        return $this->render('@Inventory/ProductCategory/show.html.twig', array(
            'productCategory' => $productCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductCategory entity.
     *
     * @Route("/{id}/edit", name="product_category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);
        $editForm = $this->createForm('InventoryBundle\Form\ProductCategoryType', $productCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCategory);
                $em->flush();

                $this->addFlash('notice', 'Product Category updated successfully.');
                return $this->redirectToRoute('product_category_edit', array('id' => $productCategory->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Product Category ' . $e->getMessage());

                return $this->render('@Inventory/ProductCategory/edit.html.twig', array(
                    'productCategory' => $productCategory,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/ProductCategory/edit.html.twig', array(
            'productCategory' => $productCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProductCategory entity.
     *
     * @Route("/{id}", name="product_category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ProductCategory $productCategory)
    {
        $form = $this->createDeleteForm($productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($productCategory);
                $em->flush();
                $this->addFlash('notice', 'Product Category deleted successfully.');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Product Category ' . $e->getMessage());

                return $this->redirectToRoute('product_category_index');
            }
        }

        return $this->redirectToRoute('product_category_index');
    }

    /**
     * Creates a form to delete a ProductCategory entity.
     *
     * @param ProductCategory $productCategory The ProductCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductCategory $productCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_category_delete', array('id' => $productCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
