<?php

namespace InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Category;
use InventoryBundle\Form\CategoryType;

/**
 * ProductCategory controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * Lists all ProductCategory entities.
     *
     * @Route("/", name="category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productCategories = $em->getRepository('InventoryBundle:Category')->findAll();

        return $this->render('@Inventory/Category/index.html.twig', array(
            'categories' => $productCategories,
        ));
    }

    /**
     * Creates a new ProductCategory entity.
     *
     * @Route("/new", name="category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm('InventoryBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                $this->addFlash('notice', 'Category created successfully.');
                return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error creating Product Category ' . $e->getMessage());

                return $this->render('@Inventory/Category/new.html.twig', array(
                    'category' => $category,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Category/new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProductCategory entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     */
    public function showAction(Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('@Inventory/Category/index.html.twig', array(
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductCategory entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm('InventoryBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                $this->addFlash('notice', 'Category updated successfully.');
                return $this->redirectToRoute('category_index', array('id' => $category->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Product Category ' . $e->getMessage());

                return $this->render('@Inventory/Category/edit.html.twig', array(
                    'category' => $category,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('@Inventory/Category/edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProductCategory entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Category $productCategory)
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

                return $this->redirectToRoute('category_index');
            }
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Creates a form to delete a ProductCategory entity.
     *
     * @param ProductCategory $productCategory The ProductCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $productCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $productCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
