<?php
// src/AppBundle/Entity/Group.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
     protected $id;
     
     public function __construct($name = '', $roles = array())
    {
        parent::__construct($name, $roles);
        // your own logic
    }
    
    public function __toString()
    {
        return $this->getName();
    }
}

