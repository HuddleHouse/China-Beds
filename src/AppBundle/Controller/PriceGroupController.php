<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\PriceGroup;
use AppBundle\Form\PriceGroupType;

/**
 * PriceGroup controller.
 *
 * @Route("/admin/price_group")
 */
class PriceGroupController extends Controller
{
    /**
     * Lists all PriceGroup entities.
     *
     * @Route("/", name="admin_price_group_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $priceGroups = $em->getRepository('AppBundle:PriceGroup')->findAll();
        $data = array();

        foreach($priceGroups as $group) {
            $data[] = array(
                'name' => $group->getName(),
                'count' => count($group->getUsers()),
                'id' => $group->getId()
            );
        }

        return $this->render('AppBundle:PriceGroup:index.html.twig', array(
            'priceGroups' => $data,
        ));
    }

    /**
     * Creates a new PriceGroup entity.
     *
     * @Route("/new", name="admin_price_group_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $priceGroup = new PriceGroup();
        $form = $this->createForm('AppBundle\Form\PriceGroupType', $priceGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($priceGroup);
                $em->flush();

                $this->addFlash('notice', 'Price Group added successfully.');
                return $this->redirectToRoute('admin_price_group_index');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error adding Price Group: ' . $e->getMessage());
                return $this->render('AppBundle:PriceGroup:new.html.twig', array(
                    'priceGroup' => $priceGroup,
                    'form' => $form->createView(),
                ));
            }
        }

        return $this->render('AppBundle:PriceGroup:new.html.twig', array(
            'priceGroup' => $priceGroup,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PriceGroup entity.
     *
     * @Route("/{id}", name="admin_price_group_show")
     * @Method("GET")
     */
    public function showAction(PriceGroup $priceGroup)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AppBundle:PriceGroup:show.html.twig', array(
            'users' => $priceGroup->getUsers(),
            'priceGroup' => $priceGroup
        ));
    }

    /**
     * Displays a form to edit an existing PriceGroup entity.
     *
     * @Route("/{id}/edit", name="admin_price_group_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PriceGroup $priceGroup)
    {
        $deleteForm = $this->createDeleteForm($priceGroup);
        $editForm = $this->createForm('AppBundle\Form\PriceGroupType', $priceGroup);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $formData = $editForm->getData();

                foreach($priceGroup->getUsers() as $user) {
                    $user->removePriceGroup($priceGroup);
                    $em->persist($priceGroup);
                }
                foreach($formData->getUsers() as $user)
                    $user->addPriceGroup($priceGroup);

                $em->persist($priceGroup);
//                foreach()
                $em->flush();

                $this->addFlash('notice', 'Price Group updated successfully.');
                return $this->render('AppBundle:PriceGroup:edit.html.twig', array(
                    'priceGroup' => $priceGroup,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating Price Group: ' . $e->getMessage());
                return $this->render('AppBundle:PriceGroup:edit.html.twig', array(
                    'priceGroup' => $priceGroup,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
        }

        return $this->render('AppBundle:PriceGroup:edit.html.twig', array(
            'priceGroup' => $priceGroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PriceGroup entity.
     *
     * @Route("/{id}/edit/prices", name="admin_price_group_edit_prices")
     * @Method({"GET", "POST"})
     */
    public function editPricesAction(Request $request, PriceGroup $priceGroup)
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        //get all products
        $statement = $connection->prepare("select p.id, p.name, p.description, p.sku, ch.name as channel_name, ch.url as channel_url, ch.id as channel_id
	from product p
		left join product_channels c
			on c.product_id = p.id
		left join channel ch 
			on c.channel_id = ch.id
		left join product_variant v
			on p.id = v.product_id
		where p.active = 1
		group by c.id");

        //will store formatted array
        $product_data = array();

        try {
            $statement->execute();
            $products = $statement->fetchAll();

            foreach($products as $product) {
                //get a list of all products
                $statement = $connection->prepare("select *, v.id as variant_id, v.msrp/100 as msrp
	from product_variant v 
		where v.product_id = :product_id");
                $statement->bindValue('product_id', $product['id']);
                $statement->execute();
                $variant_data = $statement->fetchAll();
                $variants = array();

                //loop through said list to update prices, if they exist in the db already.
                foreach($variant_data as $var) {
                    $statement = $connection->prepare("select price/100 as price from price_group_prices
	where product_variant_id = :product_variant_id
	and price_group_id = :price_group_id");
                    $statement->bindValue('product_variant_id', $var['id']);
                    $statement->bindValue('price_group_id', $priceGroup->getId());
                    $statement->execute();
                    $price = $statement->fetch();

                    if($price == false)
                        $price['price'] = 0;
                    $var['price'] = $price['price'];
                    $variants[] = $var;
                }
                $product['variants'] = $variants;

                $product_data[$product['channel_name']][] = $product;
            }


        } catch(\Exception $e) {
            $this->addFlash('error', 'Error loading Information: ' . $e->getMessage());
        }

        return $this->render('AppBundle:PriceGroup:edit-prices.html.twig', array(
            'priceGroup' => $priceGroup,
            'products' => $product_data
        ));
    }


    /**
     * Deletes a PriceGroup entity.
     *
     * @Route("/{id}", name="admin_price_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PriceGroup $priceGroup)
    {
        $form = $this->createDeleteForm($priceGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($priceGroup);
                $em->flush();
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error deleting Price Group: ' . $e->getMessage());
                return $this->redirectToRoute('admin_price_group_index');
            }
        }

        return $this->redirectToRoute('admin_price_group_index');
    }

    /**
     * Creates a form to delete a PriceGroup entity.
     *
     * @param PriceGroup $priceGroup The PriceGroup entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PriceGroup $priceGroup)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_price_group_delete', array('id' => $priceGroup->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
