<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Orders;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/{id_channel}/test/{id_order}", name="test_email")
     * @ParamConverter("channel", class="InventoryBundle:Channel", options={"id" = "id_channel"})
     * @ParamConverter("order", class="OrderBundle:Orders", options={"id" = "id_order"})
     * @Method("GET")
     */
    public function testEmailAction(Channel $channel, Orders $order)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if($em->getRepository('AppBundle:User')->canViewOrder($order, $this->getUser()) == 1) {
            $user = $this->getUser();
            $user_channels = $user->getUserChannelsArray();

            $product_data = $em->getRepository('OrderBundle:Orders')->getProductsByWarehouseArray($order);

            $groups = $user->getGroupsArray();
            $is_dis = $is_retail = 0;

            if(isset($groups['Retailer']))
                $is_retail = 1;
            if(isset($groups['Distributor']))
                $is_dis = 1;
            $pop = $order->getPopItems();

            $manualItems = $order->getManualItems();
            $manualCount = 0;
            foreach($manualItems as $manualItem) {
                $manualCount++;
            }

            return $this->render('@Order/OrderProducts/order-email-receipt.html.twig', array(
                'channel' => $channel,
                'order' => $order,
                'user' => $user,
                'product_data' => $product_data,
                'is_retail' => $is_retail,
                'is_dis' => $is_dis,
                'pop_items' => $pop,
                'is_paid' => ($order->getStatus()->getName() == 'Paid' ? 1 : 0),
                'manual_items' => $manualItems,
                'manual_items_count' => $manualCount
            ));
        }
        else
            return $this->redirectToRoute('404');
    }

    /**
     * @Route("/view-users", name="view_users")
     */
    public function viewAllUsersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:Admin:view_users.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/view-users/edit/{user_id}", name="admin_edit_user")
     */
    public function viewAdminEditUserAction(Request $request, $user_id)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id' => $user_id));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                $event = new FormEvent($form, $request);
                $userManager->updateUser($user);
                $successMessage = "User information updated succesfully.";
                $this->addFlash('notice', $successMessage);

                return $this->redirectToRoute('admin_edit_user', array('user_id' => $user_id));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating user: ' . $e->getMessage());
                return $this->render('AppBundle:Admin:admin_edit_user.html.twig', array(
                    'form' => $form->createView(),
                    'user_id' => $user_id,
                    'user' => $user
                ));
            }
        }

        return $this->render('AppBundle:Admin:admin_edit_user.html.twig', array(
            'form' => $form->createView(),
            'user_id' => $user_id,
            'user' =>$user
        ));
    }


    /**
     * @Route("/add-user", name="send_invitation")
     */
    public function sendInvitationAction(Request $request)
    {
        $invitation = new Invitation();
        $officeRepository = $this->getDoctrine()->getRepository('InventoryBundle:Office');

        $form = $this->createFormBuilder($invitation)
            ->add('email', EmailType::class)
            ->add('admin', CheckboxType::class, array(
                'label' => 'Give user admin privileges?',
                'required' => false,
            ))
            ->add('office', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Office',
                'label' => 'Office to assign to?',
                'choice_label' => 'name',
            ))
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $data = $form->getData();

            if($officeId = $data->getOffice()) {
                $office = $officeRepository->find($officeId);
                $invitation->setOffice($office);
            }
            else
                $invitation->setOffice(null);

            $email_service = $this->get('email_service');
            $email_service->sendEmail(array(
                    'subject' => 'Invitation to register for utus-orders.com',
                    'from' => 'matt@245tech.com',
                    'to' => $invitation->getEmail(),
                    'body' => $this->renderView("AppBundle:Email:send_invitation_email.html.twig", array('code' => $invitation->getCode()))
                )
            );

            $invitation->send();
            $em->persist($invitation);
            $em->flush();

            $successMessage = "Invitation to " . $invitation->getEmail() . " sent succesfully.";
            $this->addFlash('notice', $successMessage);

            $invitation = new Invitation();
            $form = $this->createFormBuilder($invitation)
                ->add('email', EmailType::class)
                ->add('admin', CheckboxType::class, array(
                    'label' => 'Give user admin privileges?',
                    'required' => false,
                ))
                ->add('office', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\Office',
                    'label' => 'Office to assign to?',
                    'choice_label' => 'name'
                ))
                ->getForm();

            return $this->render('AppBundle:Admin:send_invitation.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->render('AppBundle:Admin:send_invitation.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/add-user/sent-invitations", name="all_invitations")
     */
    public function showAllInvitationsAction(Request $request)
    {
        $conn = $this->get('database_connection');
        $invitations = $conn->fetchAll('SELECT * FROM invitation');

        return $this->render('AppBundle:Admin:invitations_sent.html.twig', array(
            'invitations' => $invitations
        ));
    }

    /**
     * @Route("/access-restriction", name="access_restriction")
     */
    public function showAccessRestrictionAction(Request $request)
    {
        /** @var $router \Symfony\Component\Routing\Router */
        $router = $this->container->get('router');
        /** @var $collection \Symfony\Component\Routing\RouteCollection */
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();
        $data = array();
        $categories = array();

        // THESE ARE THE EXCLUDED CATEGORIES
        $exclude = array(
            'login',
            'login_check',
            'logout',
            'not-found',
            '',
            'register',
            'profile',
            'resetting',
            '{site}',
        );

        foreach ($allRoutes as $route => $params)
        {
            if (substr($route, 0, 1) === '_') {

            }
            else {
                $category = explode('/', $params->getPath())[1];
                if(!in_array($category, $exclude)) {
                    $categories[] = $category;
                    $data[] = array('path' => $params->getPath(), 'route' => $route, 'category' => $category);
                }
            }
        }

        $categories = array_unique($categories);
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AppBundle:Role')->findAll();
        $connection = $em->getConnection();
        $statement = $connection->prepare("select concat(role_id, route_name) as name from role_permissions");
        $statement->execute();
        $tmp = $statement->fetchAll();
        $permissions = array();
        foreach($tmp as $t) {
            $permissions[] = $t['name'];
        }

        return $this->render('AppBundle:Admin:access_restriction.html.twig', array(
            'routes' => $data,
            'roles' => $roles,
            'permissions' => $permissions,
            'categories' => $categories
        ));
    }

    
    /**
     * @Route("/api/api-role-permission", name="api_add_role_permission")
     */
    public function apiAddRolePermissionAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $role_id = $request->request->get('role_id');
        $route = $request->request->get('route');
        $checked = $request->request->get('checked');

        $connection = $em->getConnection();
        $statement = $connection->prepare("select id from role_permissions where route_name = :route_name and role_id = :role_id");
        $statement->bindValue('route_name', $route);
        $statement->bindValue('role_id', $role_id);
        $statement->execute();
        $exists = $statement->fetch();

        if($checked && $exists == false){
            $connection = $em->getConnection();
            $statement = $connection->prepare("insert into role_permissions (route_name, role_id) values (:route_name, :role_id)");
            $statement->bindValue('route_name', $route);
            $statement->bindValue('role_id', $role_id);
            $statement->execute();
        }
        else if($checked == 0 && $exists) {
            $connection = $em->getConnection();
            $statement = $connection->prepare("delete from role_permissions where route_name = :route_name and role_id = :role_id");
            $statement->bindValue('route_name', $route);
            $statement->bindValue('role_id', $role_id);
            $statement->execute();
        }
        return JsonResponse::create(true);
}


}