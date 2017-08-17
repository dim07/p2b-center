<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SubObject
 *
 * @ORM\Table(name="sub_object")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubObjectRepository")
 */
class SubObject
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
     * @ORM\Column(name="objectId", type="integer")
     */
    private $objectId;
    
    /**
     * @ORM\ManyToOne(targetEntity="SectorObject", inversedBy="subobjects")
     * @ORM\JoinColumn(name="objectId", referencedColumnName="id")
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

     /**
     * @ORM\OneToMany(targetEntity="SubObjectItem", mappedBy="subobject")
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return SubObject
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SubObject
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
     * Set object
     *
     * @param \AppBundle\Entity\SectorObject $object
     *
     * @return SubObject
     */
    public function setObject(\AppBundle\Entity\SectorObject $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \AppBundle\Entity\SectorObject
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Add item
     *
     * @param \AppBundle\Entity\SubObjectItem $item
     *
     * @return SubObject
     */
    public function addItem(\AppBundle\Entity\SubObjectItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\SubObjectItem $item
     */
    public function removeItem(\AppBundle\Entity\SubObjectItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
    
    public function __toString() 
    {
        return $this->getName();
    }
}
