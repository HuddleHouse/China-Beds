<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseService
{
    protected $mailer;
    protected $container;

    public function __construct(\Swift_Mailer $mailer, ContainerInterface $containerInterface)
    {
        $this->mailer = $mailer;
        $this->container = $containerInterface;
    }
}
