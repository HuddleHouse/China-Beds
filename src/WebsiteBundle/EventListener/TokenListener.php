<?php
namespace WebsiteBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\TraceableControllerResolver;
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
    public function __construct($em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $channel = $this->em->getRepository('InventoryBundle\Entity\Channel')->findOneByUrl($_SERVER['SERVER_NAME']);
        
	if ( !$channel ) {
		$channel = $this->em->getRepository('InventoryBUndle\Entity\CHannel')->find(1);
            //$event->setResponse(new Response(sprintf('%s Not found!', $_SERVER['SERVER_NAME']), 404));
        }

        $this->session->set('channel_id', $channel->getId());

    }



}
