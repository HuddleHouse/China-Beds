<?php

namespace AppBundle\Controller\API;

use AppBundle\Form\NewUserType;
use OrderBundle\Entity\Orders;
use OrderBundle\Entity\OrdersProductVariant;
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
class ProfileController extends Controller
{

    /**
     * @Route("/api_create_new_user", name="api_create_new_user")
     */
    public function createNewUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $channel_name = $request->request->get('type_name');

        $type_id = $request->request->get('type_id');
        $channel = $em->getRepository('InventoryBundle:Channel')->find($type_id);

        $type_role = $request->request->get('type_role');
        $role = $em->getRepository('AppBundle:Role')->findBy(array('name' => $type_role));


        $values = $request->request->get('values');
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $data = array();
        foreach($values as $key=>$value) {
            $key = substr($key, 9);
            $data[$key] = $value;
        }
        $new_user_form = $this->createForm(NewUserType::class, $user);

        $warehouse_1 = $em->getRepository('WarehouseBundle:Warehouse')->find($this->container->getParameter('warehouse_1'));
        $warehouse_2 = $em->getRepository('WarehouseBundle:Warehouse')->find($this->container->getParameter('warehouse_2'));
        $warehouse_3 = $em->getRepository('WarehouseBundle:Warehouse')->find($this->container->getParameter('warehouse_3'));

    }

}

