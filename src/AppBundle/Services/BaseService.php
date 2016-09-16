<?php

namespace AppBundle\Services;

class BaseService
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
}
