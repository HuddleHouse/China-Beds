<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Invitationss
 *
 * @ORM\Table(name="invitation")
 * @ORM\Entity
 */
class Invitation
{
    /** @ORM\Id @ORM\Column(type="string", length=6) */
    protected $code;

    /** @ORM\Column(type="string", length=256)
     * @Assert\Email(message="Put email.")
     */
    protected $email;

    /**
     * When sending invitation be sure to set this value to `true'
     * It can prevent invitations from being sent twice
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $used = false;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $valid = true;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $admin = false;



    public function __construct()
    {
        // generate identifier only once, here a 6 characters length code
        $this->code = substr(md5(uniqid(rand(), true)), 0, 6);
    }


    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function send()
    {
        $this->sent = true;
    }

    public function useInvitation()
    {
        $this->used = true;
    }

    public function invalidate()
    {
        $this->valid = true;
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param mixed $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return mixed
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @param mixed $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param mixed $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }



}
