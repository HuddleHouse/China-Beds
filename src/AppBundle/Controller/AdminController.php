<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
{
    /**
     * @Route("/admin/view-users", name="view_users")
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
     * @Route("/admin/view-users/edit/{user_id}", name="admin_edit_user")
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

                return $this->render('AppBundle:Admin:admin_edit_user.html.twig', array(
                    'form' => $form->createView(),
                    'user_id' => $user_id
                ));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating user: ' . $e->getMessage());
                return $this->render('AppBundle:Admin:admin_edit_user.html.twig', array(
                    'form' => $form->createView(),
                    'user_id' => $user_id
                ));
            }
        }

        return $this->render('AppBundle:Admin:admin_edit_user.html.twig', array(
            'form' => $form->createView(),
            'user_id' => $user_id
        ));
    }


    /**
     * @Route("/admin/add-user", name="send_invitation")
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
     * @Route("/admin/add-user/sent-invitations", name="all_invitations")
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
     * @Route("/admin/access-restriction", name="access_restriction")
     */
    public function showAddressRestrictionAction(Request $request)
    {
        /** @var $router \Symfony\Component\Routing\Router */
        $router = $this->container->get('router');
        /** @var $collection \Symfony\Component\Routing\RouteCollection */
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();
        $data = array();

        foreach ($allRoutes as $route => $params)
        {
            if (substr($route, 0, 1) === '_') {

            }
            else {
                $data[] = array('path' => $params->getPath(), 'route' => $route);
            }
        }

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
            'permissions' => $permissions
        ));
    }

    
    /**
     * @Route("/admin/api/api-role-permission", name="api_add_role_permission")
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