<?php

namespace InventoryBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Warehouse;
use InventoryBundle\Form\WarehouseType;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse/inventory")
 */
class WarehouseInventoryController extends Controller
{
    /**
     * Lists all Warehouse entities.
     *
     * @Route("/", name="warehouse_inventory_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $warehouses = $em->getRepository('InventoryBundle:Warehouse')->findAll();

        return $this->render('@Inventory/Warehouse/index.html.twig', array(
            'warehouses' => $warehouses,
        ));
    }

//    /**
//     * Creates a new Warehouse entity.
//     *
//     * @Route("/new", name="warehouse_new")
//     * @Method({"GET", "POST"})
//     */
//    public function newAction(Request $request)
//    {
//        $warehouse = new Warehouse();
//        $form = $this->createForm('InventoryBundle\Form\WarehouseType', $warehouse);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            try {
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($warehouse);
//                $em->flush();
//                $this->addFlash('notice', 'Warehouse created successfully.');
//                return $this->redirectToRoute('warehouse_edit', array('id' => $warehouse->getId()));
//            }
//            catch(\Exception $e) {
//                $this->addFlash('error', 'Error creating warehouse ' . $e->getMessage());
//
//                return $this->render('@Inventory/Warehouse/new.html.twig', array(
//                    'warehouse' => $warehouse,
//                    'form' => $form->createView(),
//                ));
//            }
//        }
//
//        return $this->render('@Inventory/Warehouse/new.html.twig', array(
//            'warehouse' => $warehouse,
//            'form' => $form->createView(),
//        ));
//    }
//
//    /**
//     * Finds and displays a Warehouse entity.
//     *
//     * @Route("/{id}", name="warehouse_show")
//     * @Method("GET")
//     */
//    public function showAction(Warehouse $warehouse)
//    {
//        $deleteForm = $this->createDeleteForm($warehouse);
//
//        return $this->render('@Inventory/Warehouse/show.html.twig', array(
//            'warehouse' => $warehouse,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Displays a form to edit an existing Warehouse entity.
//     *
//     * @Route("/{id}/edit", name="warehouse_edit")
//     * @Method({"GET", "POST"})
//     */
//    public function editAction(Request $request, Warehouse $warehouse)
//    {
//        $deleteForm = $this->createDeleteForm($warehouse);
//        $editForm = $this->createForm('InventoryBundle\Form\WarehouseType', $warehouse);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            try {
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($warehouse);
//                $em->flush();
//
//                $this->addFlash('notice', 'Warehouse updated successfully.');
//                return $this->redirectToRoute('warehouse_index', array('id' => $warehouse->getId()));
//            }
//            catch(\Exception $e) {
//                $this->addFlash('error', 'Error updating warehouse ' . $e->getMessage());
//
//                return $this->render('@Inventory/Warehouse/edit.html.twig', array(
//                    'warehouse' => $warehouse,
//                    'form' => $editForm->createView(),
//                    'delete_form' => $deleteForm->createView(),
//                ));
//            }
//        }
//
//        return $this->render('@Inventory/Warehouse/edit.html.twig', array(
//            'warehouse' => $warehouse,
//            'form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }
//
//    /**
//     * Deletes a Warehouse entity.
//     *
//     * @Route("/{id}", name="warehouse_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, Warehouse $warehouse)
//    {
//        $form = $this->createDeleteForm($warehouse);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            try {
//                $em = $this->getDoctrine()->getManager();
//                $em->remove($warehouse);
//                $em->flush();
//            }
//            catch(\Exception $e) {
//                $this->addFlash('error', 'Error deleting warehouse ' . $e->getMessage());
//
//                return $this->redirectToRoute('warehouse_index');
//            }
//        }
//
//        return $this->redirectToRoute('warehouse_index');
//    }
//
//    /**
//     * Creates a form to delete a Warehouse entity.
//     *
//     * @param Warehouse $warehouse The Warehouse entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm(Warehouse $warehouse)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('warehouse_delete', array('id' => $warehouse->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
}
