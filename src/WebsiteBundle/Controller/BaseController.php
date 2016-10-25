<?php
/**
 * Created by PhpStorm.
 * User: jeremib
 * Date: 10/24/16
 * Time: 4:29 PM
 */

namespace WebsiteBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    public function getChannel() {
        if ( $channel_id = $this->container->get('session')->get('channel_id') ) {
            return $this->container->get('doctrine')->getManager()->getRepository('InventoryBundle\Entity\Channel')->find($channel_id);
        }

    }
}