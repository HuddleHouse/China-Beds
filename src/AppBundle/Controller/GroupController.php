<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseGroupEvent;
use FOS\UserBundle\Event\GroupEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing group CRUD
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class GroupController extends Controller
{
    /**
     * Show all groups
     */
    public function listAction()
    {
        $groups = $this->get('fos_user.group_manager')->findGroups();

        foreach($groups as $group) {
            $data[] = array(
                'name' => $group->getName(),
                'count' => count($group->getUsers()),
                'id' => $group->getId()
            );
        }

        return $this->render('FOSUserBundle:Group:list.html.twig', array(
            'groups' => $data,
        ));
    }

    /**
     * Show one group
     */
    public function showAction($groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        return $this->render('FOSUserBundle:Group:show.html.twig', array(
            'users' => $group->getUsers()
        ));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseGroupEvent($group, $request);
        $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_INITIALIZE, $event);

        if(null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');

        $form = $formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
                $groupManager = $this->get('fos_user.group_manager');

                $oldRole = $group->getRoles();

                $name = str_replace(' ', '_', $group->getName());
                $name = preg_replace("/[^A-Za-z0-9_]/", '', $name);
                $name = strtoupper($name);

                $role = "ROLE_" . $name;
                $group->setRoles(array());
                $group->addRole($role);

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_SUCCESS, $event);
                $groupManager->updateGroup($group);

                if(null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_group_show', array('groupName' => $group->getName()));
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

                $this->addFlash('notice', 'Role updated successfully.');

                return $this->redirectToRoute('fos_user_group_list');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating role: ' . $e->getMessage());

                return $this->render('FOSUserBundle:Group:edit.html.twig', array(
                    'form' => $form->createview(),
                    'group_name' => $group->getName(),
                    'form_path' => 'fos_user_group_edit'
                ));
            }
        }

        return $this->render('FOSUserBundle:Group:edit.html.twig', array(
            'form' => $form->createview(),
            'group_name' => $group->getName(),
            'form_path' => 'fos_user_group_edit'
        ));
    }

    /**
     * Show the new form
     */
    public function newAction(Request $request)
    {
        /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
        $groupManager = $this->get('fos_user.group_manager');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $group = $groupManager->createGroup('');

        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

        $form = $formFactory->createForm();
        $form->setData($group);
        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

                $role = "ROLE_" . strtoupper($group->getName());
                $group->setRoles(array());
                $group->addRole($role);
                $groupManager->updateGroup($group);

                $success = $group->getName() . " successfully created.";

                $group = $groupManager->createGroup('');

                $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

                $form2 = $formFactory->createForm();
                $form2->setData($group);

                $this->addFlash('notice', $success);

                return $this->redirectToRoute('fos_user_group_list');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating role: ' . $e->getMessage());

                return $this->render('FOSUserBundle:Group:new.html.twig', array(
                    'form' => $form->createview(),
                    'groupName' => '',
                    'form_path' => 'fos_user_group_new'
                ));
            }
        }
        return $this->render('FOSUserBundle:Group:new.html.twig', array(
            'form' => $form->createview(),
            'groupName' => '',
            'form_path' => 'fos_user_group_new'
        ));

    }

    /**
     * Delete one group
     */
    public function deleteAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);
        $this->get('fos_user.group_manager')->deleteGroup($group);

        $response = new RedirectResponse($this->generateUrl('fos_user_group_list'));

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::GROUP_DELETE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

        return $response;
    }

    /**
     * Find a group by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     *
     * @throws NotFoundHttpException                if user does not exist
     * @return \FOS\UserBundle\Model\GroupInterface
     */
    protected function findGroupBy($key, $value)
    {
        if(!empty($value)) {
            $group = $this->get('fos_user.group_manager')->{'findGroupBy' . ucfirst($key)}($value);
        }

        if(empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }
}
