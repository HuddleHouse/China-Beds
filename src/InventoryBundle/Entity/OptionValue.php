<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OptionValues
 *
 * @ORM\Table(name="option_values")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\OptionValuesRepository")
 */
class OptionValue
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
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;



    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\ManageOption", inversedBy="option_values")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    private $option;


    /**
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param mixed $option
     */
    public function setOption($option)
    {
        $this->option = $option;
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
     * Set value
     *
     * @param string $value
     *
     * @return OptionValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set optionId
     *
     * @param integer $optionId
     *
     * @return OptionValues
     */
    public function setOptionId($optionId)
    {
        $this->option_id = $optionId;

        return $this;
    }

    /**
     * Get optionId
     *
     * @return int
     */
    public function getOptionId()
    {
        return $this->option_id;
    }
}
