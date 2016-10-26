<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class SettingsService
{
    private $em;

    /**
     * SettingsService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $name
     * @return \AppBundle\Entity\Settings[]|array|string
     */
    public function get($name) {
        try {
            $rtn = $this->em->getRepository('AppBundle:Settings')->findOneBy(array('name' => $name))->getValue();
        }
        catch(\Exception $e) {
            return "Could not find setting " . $name . ": " . $e->getMessage();
        }

        return $rtn;
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