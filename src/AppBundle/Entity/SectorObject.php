<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SectorObject
 *
 * @ORM\Table(name="sector_object")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectorObjectRepository")
 */
class SectorObject
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
     * @ORM\Column(name="sectorId", type="integer")
     */
    private $sectorId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="objects")
     * @ORM\JoinColumn(name="sectorId", referencedColumnName="id")
     */
    private $sector;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

     /**
     * @ORM\OneToMany(targetEntity="SubObject", mappedBy="object")
     */
    private $subobjects;
    
    public function __construct()
    {
        $this->subobjects = new ArrayCollection();
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
     * Set sectorId
     *
     * @param integer $sectorId
     *
     * @return SectorObject
     */
    public function setSectorId($sectorId)
    {
        $this->sectorId = $sectorId;

        return $this;
    }

    /**
     * Get sectorId
     *
     * @return int
     */
    public function getSectorId()
    {
        return $this->sectorId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SectorObject
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
     * Set sector
     *
     * @param \AppBundle\Entity\Sector $sector
     *
     * @return SectorObject
     */
    public function setSector(\AppBundle\Entity\Sector $sector = null)
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get sector
     *
     * @return \AppBundle\Entity\Sector
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Add subobject
     *
     * @param \AppBundle\Entity\SubObject $subobject
     *
     * @return SectorObject
     */
    public function addSubobject(\AppBundle\Entity\SubObject $subobject)
    {
        $this->subobjects[] = $subobject;

        return $this;
    }

    /**
     * Remove subobject
     *
     * @param \AppBundle\Entity\SubObject $subobject
     */
    public function removeSubobject(\AppBundle\Entity\SubObject $subobject)
    {
        $this->subobjects->removeElement($subobject);
    }

    /**
     * Get subobjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubobjects()
    {
        return $this->subobjects;
    }
    
    public function __toString() 
    {
        return $this->getName();
    }
}
