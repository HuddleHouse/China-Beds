<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Matt Drollette <matt@drollette.com>
 */
class CoreExtensions extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
   
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array();
    }

    public function getFilters()
    {
        return array(
            'latlong' => new \Twig_Filter_Method($this, 'latLongConvert'),
        );
    }
    
    public function latLongConvert($address, $address2, $city)
    {
          
            $url = "http://maps.google.com/maps/api/geocode/json?address=$city&sensor=false&region=false";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response);

            $lat = 0;
            $long = 0;

            if (is_object($response_a)) {
                if (count($response_a->results)>0) {
                    $lat = $response_a->results[0]->geometry->location->lat;
                    $long = $response_a->results[0]->geometry->location->lng;
                }
            }
            
            return array($lat, $long);
            
    }


    public function getName()
    {
        return 'conceptrix_core_locale';
    }
}