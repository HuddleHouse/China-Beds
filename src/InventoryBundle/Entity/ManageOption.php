<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ManageOption
 *
 * @ORM\Table(name="options")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ManageOptionRepository")
 */
class ManageOption
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;
    

    /**
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\OptionValue", mappedBy="option")
     */
    private $option_values;
    
    public function __construct() {
        $this->option_values = new ArrayCollection();
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
     * @return ManageOption
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setCode(preg_replace("/[^a-zA-Z0-9]+/", "", $name));

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ManageOption
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add optionValue
     *
     * @param \InventoryBundle\Entity\OptionValue $optionValue
     *
     * @return ManageOption
     */
    public function addOptionValue(\InventoryBundle\Entity\OptionValue $optionValue)
    {
        $this->option_values[] = $optionValue;

        return $this;
    }

    /**
     * Remove optionValue
     *
     * @param \InventoryBundle\Entity\OptionValue $optionValue
     */
    public function removeOptionValue(\InventoryBundle\Entity\OptionValue $optionValue)
    {
        $this->option_values->removeElement($optionValue);
    }

    /**
     * Get optionValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptionValues()
    {
        return $this->option_values;
    }
}
