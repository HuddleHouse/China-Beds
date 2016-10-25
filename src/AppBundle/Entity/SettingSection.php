<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingSection
 *
 * @ORM\Table(name="setting_section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingSectionRepository")
 */
class SettingSection
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Settings", mappedBy="section")
     */
    private $settings;

    public function __construct()
    {
        $this->settings = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SettingSection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name . ' Settings';
    }

    /**
     * @return Settings[]
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param Settings[] $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}

