<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LegalEntity
 *
 * @ORM\Table(name="legal_entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LegalEntityRepository")
 */
class LegalEntity
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
     * @ORM\Column(name="Name", type="string", length=80, unique=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="HideName", type="boolean")
     */
    private $hideName;

    /**
     * @var string
     *
     * @ORM\Column(name="LegalAdr", type="string", length=255, nullable=true)
     */
    private $legalAdr;

    /**
     * @var bool
     *
     * @ORM\Column(name="customer", type="boolean", nullable=true)
     */
    private $customer;

    /**
     * @var bool
     *
     * @ORM\Column(name="contractor", type="boolean", nullable=true)
     */
    private $contractor;
    
    /**
     * @ORM\OneToMany(targetEntity="project", mappedBy="Contractor")
     */
    private $contractprojects;
    
    /**
     * @ORM\OneToMany(targetEntity="StageOrder", mappedBy="Contractor")
     */
    private $contractorders;
    
    /**
     * @ORM\OneToMany(targetEntity="project", mappedBy="Customer")
     */
    private $customprojects;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;
    
    /**
     * 
     * @ORM\OneToOne(targetEntity="User", inversedBy="legentity")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $user;

    
    public function __construct()
    {
        $this->contractprojects = new ArrayCollection();
        $this->contractorders = new ArrayCollection();
        $this->customprojects = new ArrayCollection();
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
     * @return LegalEntity
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
     * Set hideName
     *
     * @param boolean $hideName
     *
     * @return LegalEntity
     */
    public function setHideName($hideName)
    {
        $this->hideName = $hideName;

        return $this;
    }

    /**
     * Get hideName
     *
     * @return bool
     */
    public function getHideName()
    {
        return $this->hideName;
    }

    /**
     * Set legalAdr
     *
     * @param string $legalAdr
     *
     * @return LegalEntity
     */
    public function setLegalAdr($legalAdr)
    {
        $this->legalAdr = $legalAdr;

        return $this;
    }

    /**
     * Get legalAdr
     *
     * @return string
     */
    public function getLegalAdr()
    {
        return $this->legalAdr;
    }

    /**
     * Set customer
     *
     * @param boolean $customer
     *
     * @return LegalEntity
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return bool
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set contractor
     *
     * @param boolean $contractor
     *
     * @return LegalEntity
     */
    public function setContractor($contractor)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return bool
     */
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return LegalEntity
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return LegalEntity
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Add contractproject
     *
     * @param \AppBundle\Entity\project $contractproject
     *
     * @return LegalEntity
     */
    public function addContractproject(\AppBundle\Entity\project $contractproject)
    {
        $this->contractprojects[] = $contractproject;

        return $this;
    }

    /**
     * Remove contractproject
     *
     * @param \AppBundle\Entity\project $contractproject
     */
    public function removeContractproject(\AppBundle\Entity\project $contractproject)
    {
        $this->contractprojects->removeElement($contractproject);
    }

    /**
     * Get contractprojects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContractprojects()
    {
        return $this->contractprojects;
    }

    /**
     * Add customproject
     *
     * @param \AppBundle\Entity\project $customproject
     *
     * @return LegalEntity
     */
    public function addCustomproject(\AppBundle\Entity\project $customproject)
    {
        $this->customprojects[] = $customproject;

        return $this;
    }

    /**
     * Remove customproject
     *
     * @param \AppBundle\Entity\project $customproject
     */
    public function removeCustomproject(\AppBundle\Entity\project $customproject)
    {
        $this->customprojects->removeElement($customproject);
    }

    /**
     * Get customprojects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomprojects()
    {
        return $this->customprojects;
    }

    /**
     * Add contractorder
     *
     * @param \AppBundle\Entity\StageOrder $contractorder
     *
     * @return LegalEntity
     */
    public function addContractorder(\AppBundle\Entity\StageOrder $contractorder)
    {
        $this->contractorders[] = $contractorder;

        return $this;
    }

    /**
     * Remove contractorder
     *
     * @param \AppBundle\Entity\StageOrder $contractorder
     */
    public function removeContractorder(\AppBundle\Entity\StageOrder $contractorder)
    {
        $this->contractorders->removeElement($contractorder);
    }

    /**
     * Get contractorders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContractorders()
    {
        return $this->contractorders;
    }
    
    public function __toString() 
    {
        return $this->getName();
    }
}
