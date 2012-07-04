<?php
// src/Ace/TaskBundle/Entity/Prereg.php
namespace Ace\MiscBundle\Entity;

class Prereg
{
    protected $name;

    protected $username;

    protected $email;

    protected $site;

    protected $description;

	protected $reason;

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSite()
    {
        return $this->site;
    }
    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getReason()
    {
        return $this->reason;
    }
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

}