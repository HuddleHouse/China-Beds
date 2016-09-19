<?php

namespace AppBundle\Services;

use AppBundle\Services\BaseService;

class EmailService extends BaseService
{
    public function sendEmail($data)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody($data['body'], 'text/html');

        $this->mailer->send($message);
    }
}