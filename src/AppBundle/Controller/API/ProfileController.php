<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\User;
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
        if($type_role === 'Retailer')
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => $type_role));
        else if ($type_role == 'Distributor')
            $role = 'ROLE_DISTRIBUTOR';
        else if ($type_role === 'Sales Rep')
            $role = 'ROLE_SALES_REP';

        $values = $request->request->get('values');
//        $user = new User();

        $userManager = $this->container->get('fos_user.user_manager');
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
        $distributor = $em->getRepository('AppBundle:User')->find($this->container->getParameter('default_distributor'));
        $sales_rep = $em->getRepository('AppBundle:User')->find($this->container->getParameter('default_sales_rep'));
        $sales_manager = $em->getRepository('AppBundle:User')->find($this->container->getParameter('default_sales_manager'));

//        $email_service = $this->get('email_service');
//        $email_service->sendEmail(array(
//                'subject' => 'You have registered!',
//                'from' => 'matt@245tech.com',
//                'to' => 'mthuddleston@gmail.com',
//                'body' => $this->renderView(
//                    'AppBundle:Emails:new-user.html.twig',
//                    array('name' => 'Matt',
//                        'username' => 'huddlehouse',
//                        'password' => 'XLdjn345kj'
//                    )
//                )
//            )
//        );

        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setCompanyName($data['company_name']);
        $user->setIsCurrentRetailer($data['is_current_retailer']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setAddress1($data['address_1']);
        $user->setAddress2($data['address_2']);
        $user->setCity($data['city']);
        $user->setState($em->getRepository('AppBundle:State')->find($data['state']));
        $user->setZip($data['zip']);
        $user->setIsResidential($data['is_residential']);
        $user->setIsOnlineIntentions($data['is_online_intentions']);
        $user->setOnlineWebUrl($data['online_web_url']);
        $user->setPlainPassword('matt');

        $user->setWarehouse1($warehouse_1);
        $user->setWarehouse2($warehouse_2);
        $user->setWarehouse3($warehouse_3);

        try {
            $user->setMyDistributor($distributor);
            $distributor->addRetailer($user);
            $em->persist($distributor);

            $user->setMySalesRep($sales_rep);
            $sales_rep->addRetailer($user);
            $em->persist($sales_rep);

            $user->addUserChannel($channel);
            $user->addGroup($role);

            $userManager->updateUser($user);
            return JsonResponse::create(true);
        }
        catch(\Exception $e) {
            return JsonResponse::create(false);
        }



    }

}

