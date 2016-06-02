<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route  = $request->attributes->get('_route');
        $routeArr = array('fos_js_routing_js', 'fos_user_security_login', '_wdt'); //These are excluded routes. These are always allowed. Required for login page
        $roleArr = $this->token_storage->getToken()->getUser()->getRoles();

        if(!is_int(array_search($route, $routeArr))) //This is for excluding routes that you don't want to check for.
        {
            //Check for a matching role and route
            $qb = $this->em->getRepository('AppBundle:UserAccess')->createQueryBuilder('o');
            $qb
                ->select('o')
                ->where('o.ROLENAME IN (:roleArr)')
                ->setParameter('roleArr', $roleArr)
                ->andWhere('o.ROUTENAME = :route')
                ->setParameter('route', $route)
            ;
            $result = $qb->getQuery()->getArrayResult();
            if(empty($result))
            {
                //A matching role and route was not found so we do not give access to the user here and redirect to another page.
                $event->setResponse(new RedirectResponse($this->router->generate('user_management_unauthorized_user', array())));
            }
        }
    }
}