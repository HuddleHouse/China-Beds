<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TokenListener
{
    protected $em;
    protected $token_storage;
    protected $templating;
    protected $router;
    protected $resolver;
    public function __construct($em,TokenStorageInterface $token_storage, TwigEngine $templating, Router $router, ControllerResolver $resolver, Session $session)
    {
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->templating = $templating;
        $this->router = $router;
        $this->resolver = $resolver;
        $this->session = $session;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route  = $request->attributes->get('_route');

        $routeArr = array(
            '404',
            '_wdt',
            'fos_user_security_logout',
            'fos_user_security_login',
            'fos_user_security_check',
            'fos_user_resetting_request',
            'fos_user_resetting_send_email',
            'fos_user_resetting_check_email ',
            'fos_user_resetting_reset',
            'api_add_option_value',
            'api_get_values',
            'quickbooks_default_index',
            'fos_user_profile_show'
        ); //These are excluded routes. These are always allowed. Required for login page


        if(!is_int(array_search($route, $routeArr)) && $route != null) //This is for excluding routes that you don't want to check for.
        {
            if(null !== $this->session->get('route_names')) {
                $route_names = explode(',', $this->session->get('route_names'));
            }
            else {
                if(!($user = $this->token_storage->getToken()->getUser())) {
                    $event->setResponse(new RedirectResponse($this->router->generate('fos_user_security_login', array())));
                }
                $route_names = $user->getRouteNames();

//                setcookie('route_names', implode(',', $route_names), time()+3600);
                $this->session->set('route_names', implode(',', $route_names));
            }

            $roles = $this->token_storage->getToken()->getRoles();
            $roleData = array();

            foreach($roles as $role) {
                $roleData[$role->getRole()] = $role->getRole();
            }


            if(!in_array($route, $route_names) && !in_array('ROLE_ADMIN', $roleData))
            {
                //A matching role and route was not found so we do not give access to the user here and redirect to another page.
                $event->setResponse(new RedirectResponse($this->router->generate('404')));
            }
        }
    }



}