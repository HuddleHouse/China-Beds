<?php


namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\EditChildUserType;
use AppBundle\Form\EditUserSettingsType;
use AppBundle\Form\NewUserType;
use AppBundle\Form\UserType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
            $retailers = $retailers_data->getUsersForChannel($this->getUser()->getActiveChannel());

            $sales_reps_data = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Sales Rep'));
            $sales_reps = $sales_reps_data->getUsersForChannel($this->getUser()->getActiveChannel());

            $distributors_data = $em->getRepository('AppBundle:Role')->findOneBy(array('name' => 'Distributor'));
            $distributors = $distributors_data->getUsersForChannel($this->getUser()->getActiveChannel());

        }
        else if($this->getUser()->hasRole('ROLE_SALES_REP')) {
            $distributors = $this->getUser()->getDistributors();
            $retailers = array();
            $sales_reps = $this->getUser()->getSalesReps();

            foreach($distributors as $distributor)
                foreach($distributor->getRetailers() as $retailer)
                    $retailers[] = $retailer;
        }
        else if($this->getUser()->hasRole('ROLE_SALES_MANAGER')) {
            $sales_reps = $this->getUser()->getSalesReps();
            $distributors = array();
            $retailers = array();

            foreach($sales_reps as $sales_rep)
                foreach($sales_rep->getDistributors() as $distributor)
                $distributors[] = $distributor;

            foreach($distributors as $distributor)
                foreach($distributor->getRetailers() as $retailer)
                $retailers[] = $retailer;
        }
        else {
            $retailers = $this->getUser()->getRetailers();
            $sales_reps = $this->getUser()->getSalesReps();
            $distributors = $this->getUser()->getDistributors();
        }

        $orders = $em->getRepository('AppBundle:User')->getLatestOrdersForUser($this->getUser());

        return $this->render('AppBundle:Profile:show.html.twig', array(
            'user' => $user,
            'new_user_form' => $new_user_form->createView(),
            'retailers' => $retailers,
            'sales_reps' => $sales_reps,
            'distributors' => $distributors,
            'orders' => $orders
        ));
    }

    /**
     *
     * @Route("/user/edit/{id}", name="edit_child_user")
     */
    public function showUser(User $user, Request $request) {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user_id = $user->getId();

        $form = $this->createForm(EditChildUserType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                $event = new FormEvent($form, $request);
                $userManager->updateUser($user);
                $successMessage = "User information updated succesfully.";
                $this->addFlash('notice', $successMessage);

                return $this->redirectToRoute('edit_child_user', array('id' => $user->getId()));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating user: ' . $e->getMessage());
                return $this->render('AppBundle:Profile:edit_child_user.html.twig', array(
                    'form' => $form->createView(),
                    'user_id' => $user_id,
                    'user' =>$user
                ));
            }
        }
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */

        $form = $this->createForm(EditUserSettingsType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                $event = new FormEvent($form, $request);
                $userManager->updateUser($user);
                $successMessage = "User information updated succesfully.";
                $this->addFlash('notice', $successMessage);

                return $this->redirectToRoute('fos_user_profile_edit', array('user_id' => $user_id));
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Error updating user: ' . $e->getMessage());
                return $this->render('@App/Profile/edit.html.twig', array(
                    'form' => $form->createView(),
                    'user_id' => $user->getId(),
                    'user' => $user
                ));
            }
        }

        return $this->render('@App/Profile/edit.html.twig', array(
            'form' => $form->createView(),
            'user_id' => $user->getId(),
            'user' =>$user
        ));
    }
}
