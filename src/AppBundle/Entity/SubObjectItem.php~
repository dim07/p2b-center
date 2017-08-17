<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubObjectItem
 *
 * @ORM\Table(name="sub_object_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubObjectItemRepository")
 */
class SubObjectItem
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
     * @var int
     *
     * @ORM\Column(name="subObjectId", type="integer")
     */
    private $subObjectId;
    
    /**
     * @ORM\ManyToOne(targetEntity="SubObject", inversedBy="items")
     */
    private $subobject;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set subObjectId
     *
     * @param integer $subObjectId
     *
     * @return SubObjectItem
     */
    public function setSubObjectId($subObjectId)
    {
        $this->subObjectId = $subObjectId;

        return $this;
    }

    /**
     * Get subObjectId
     *
     * @return int
     */
    public function getSubObjectId()
    {
        return $this->subObjectId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SubObjectItem
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
        return $this->name;
    }

    /**
     * Set subobject
     *
     * @param \AppBundle\Entity\SubObject $subobject
     *
     * @return SubObjectItem
     */
    public function setSubobject(\AppBundle\Entity\SubObject $subobject = null)
    {
        $this->subobject = $subobject;

        return $this;
    }

    /**
     * Get subobject
     *
     * @return \AppBundle\Entity\SubObject
     */
    public function getSubobject()
    {
        return $this->subobject;
    }
    
    public function __toString() 
    {
        return $this->getName();
    }
}
