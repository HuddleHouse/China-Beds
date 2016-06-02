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
        $roleArr = $this->token_storage->getToken()->getUser()->getRoles();

        $routeArr = array('fos_js_routing_js', 'fos_user_security_login', '_wdt'); //These are excluded routes. These are always allowed. Required for login page

        $connection = $this->em->getConnection();
        $statement = $connection->prepare("select u.role_id from role_users u where u.user_id = :id");
        $statement->bindValue('id', $user->getId());
        $statement->execute();
        $role_ids_temp = $statement->fetchAll();
        $role_ids = array();

//        foreach($role_ids_temp as $role) {
//            $role_ids[] = $role['role_id'];
//        }

        if(!is_int(array_search($route, $routeArr))) //This is for excluding routes that you don't want to check for.
        {
//Check for a matching role and route
//            $qb = $this->em->getRepository('AppBundle:UserAccess')->createQueryBuilder('o');
//            $qb
//                ->select('o')
//                ->where('o.ROLENAME IN (:roleArr)')
//                ->setParameter('roleArr', $roleArr)
//                ->andWhere('o.ROUTENAME = :route')
//                ->setParameter('route', $route)
//            ;

            $connection = $this->em->getConnection();
            $statement = $connection->prepare("select u.role_id from role_users u where u.user_id = :id");
            $statement->bindValue('id', $user->getId());
            $statement->execute();
            $role_ids_temp = $statement->fetchAll();

            $result = $qb->getQuery()->getArrayResult();
            $result = 1234;
            if(empty($result))
            {
                //A matching role and route was not found so we do not give access to the user here and redirect to another page.
                $event->setResponse(new RedirectResponse($this->router->generate('fos_user_security_login', array())));
            }
        }
    }
}