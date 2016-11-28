<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Office
 *
 * @ORM\Table(name="price_groups")
 * @ORM\Entity
 */
class PriceGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="price_groups")
     *
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PriceGroupPrices", mappedBy="price_group", cascade={"remove", "persist"})
     */
    private $prices;


    /**
     * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Channel", inversedBy="price_group")
     */
    private $channel;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->prices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    


    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove payTypes
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get payTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param mixed $prices
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;
    }



    /**
     * Add price
     *
     * @param \AppBundle\Entity\PriceGroupPrices $price
     *
     * @return PriceGroup
     */
    public function addPrice(\AppBundle\Entity\PriceGroupPrices $price)
    {
        $this->prices[] = $price;

        return $this;
    }

    /**
     * Remove price
     *
     * @param \AppBundle\Entity\PriceGroupPrices $price
     */
    public function removePrice(\AppBundle\Entity\PriceGroupPrices $price)
    {
        $this->prices->removeElement($price);
    }

    /**
     * Set channel
     *
     * @param \InventoryBundle\Entity\Channel $channel
     *
     * @return PriceGroup
     */
    public function setChannel(\InventoryBundle\Entity\Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return \InventoryBundle\Entity\Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
