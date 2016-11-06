<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class SettingsService
{
    private $em;
    private $defaults;

    /**
     * SettingsService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        //put default settings here
        $this->defaults = array(
            'default-warehouse' => 'BB Chattanooga WH Textile Lane',
            'user-receipt' => 'no',
            'warehouse-receipt' => 'no',
            'warrantyclaim-acknowledgement' => 'no',
            'warehouse-po-eta' => 'no',
            'warehouse-po-reminder' => 'no',
        );
    }

    /**
     * @param $name
     * @return \AppBundle\Entity\Settings[]|array|string
     */
    public function get($name) {
        try {
            if ( $setting = $this->em->getRepository('AppBundle:Settings')->findOneBy(array('name' => $name)) ) {
                $rtn = $setting->getValue();
            }
            if($rtn == null)
                throw new \Exception();

            return $rtn;
        }
        catch(\Exception $e) {
            if(array_key_exists($name, $this->defaults))
                return $this->defaults[$name];
            throw new \Exception("Could not find setting " . $name . ": " . $e->getMessage());
        }
    }

    /**
     * @param $name
     * @param $value
     * @return bool|string
     */
    public function set($name, $value) {
        try {
            $setting = $this->em->getRepository('AppBundle:Settings')->findOneBy(array('name' => $name));
            $setting->setValue($value);
            $this->em->persist($setting);
            $this->em->flush();
        }
        catch(\Exception $e) {
            return "Error updating setting " . $name . ": " . $e->getMessage();
        }
        return true;
    }
}