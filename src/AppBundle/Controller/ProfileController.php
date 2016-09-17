<?php


namespace AppBundle\Controller;

use AppBundle\Form\NewUserType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $route_names = $user->getRouteNames();
        $user_channels = $user->getUserChannelsArray();

        $session = new Session();
        $session->set('user_channels', $user_channels);
        $session->set('route_names', implode(',', $route_names));

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $new_user_form = $this->createForm(NewUserType::class, $user);

        //get the correct data to be shown.
        if($this->getUser()->hasRole('ROLE_ADMIN')) {
            $retailers_data = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Retailer'));
            $retailers = $retailers_data->getUsers();

            $sales_reps_data = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Sales Rep'));
            $sales_reps = $sales_reps_data->getUsers();

            $distributors_data = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Distributor'));
            $distributors = $distributors_data->getUsers();
        }
        else {
            $retailers = $this->getUser()->getRetailers();
            $sales_reps = $this->getUser()->getSalesReps();
            $distributors = $this->getUser()->getDistributors();
        }



        foreach($retailers as $retailer)
            $i = 1;

        return $this->render('AppBundle:Profile:show.html.twig', array(
            'user' => $user,
            'new_user_form' => $new_user_form->createView(),
            'retailers' => $retailers,
            'sales_reps' => $sales_reps,
            'distributors' => $distributors
        ));
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $this->addFlash('notice', 'Information updated successfully.');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating information: ' . $e->getMessage());
                return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
                    'form' => $form->createView()
                ));
            }
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
