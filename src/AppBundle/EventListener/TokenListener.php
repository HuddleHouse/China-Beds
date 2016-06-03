<?php
namespace AppBundle\EventListener;

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
    public function __construct($em,TokenStorageInterface $token_storage, TwigEngine $templating, Router $router, ControllerResolver $resolver)
    {
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->templating = $templating;
        $this->router = $router;
        $this->resolver = $resolver;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route  = $request->attributes->get('_route');

        $routeArr = array(
            'fos_user_security_logout', 
            'fos_user_security_login', 
            'fos_user_security_check', 
            'fos_user_profile_edit', 
            'fos_user_profile_show',
            'fos_user_resetting_request',
            'fos_user_resetting_send_email',
            'fos_user_resetting_check_email ',
            'fos_user_resetting_reset'
        ); //These are excluded routes. These are always allowed. Required for login page


        if(!is_int(array_search($route, $routeArr))) //This is for excluding routes that you don't want to check for.
        {
            if(!($user = $this->token_storage->getToken()->getUser())) {
                $event->setResponse(new RedirectResponse($this->router->generate('fos_user_security_login', array())));
            }

            $result = $user->hasAccessToRoute($route);
            if($result == false)
            {
                //A matching role and route was not found so we do not give access to the user here and redirect to another page.
                $event->setResponse(new RedirectResponse($this->router->generate('404', array())));
            }
        }
    }
}