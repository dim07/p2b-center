<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
}
