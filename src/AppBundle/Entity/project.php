<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\projectRepository")
 */
class project
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer")
     */
    private $CustomerId;
    
    /**
     * @ORM\OneToOne(targetEntity="LegalEntity")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $Customer;

    /**
     * @var int
     *
     * @ORM\Column(name="contractor_id", type="integer", nullable=true)
     */
    private $ContractorId;
    
    /**
     * @ORM\OneToOne(targetEntity="LegalEntity")
     * @ORM\JoinColumn(name="contractor_id", referencedColumnName="id")
     */
    private $Contractor;

    /**
     * @var int
     *
     * @ORM\Column(name="gip_id", type="integer", nullable=true)
     */
    private $GipId;
    
    /**
     * @ORM\OneToOne(targetEntity="NaturPers")
     * @ORM\JoinColumn(name="gip_id", referencedColumnName="id")
     */
    private $Gip;

    /**
     * @var float
     *
     * @ORM\Column(name="nofot", type="float", nullable=true)
     */
    private $nofot;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;
    
     /**
     * @ORM\OneToMany(targetEntity="ProjectStage", mappedBy="project")
     */
    private $stages;
    
    public function __construct()
    {
        $this->stages = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return project
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
     * Set customerId
     *
     * @param integer $customerId
     *
     * @return project
     */
    public function setCustomerId($customerId)
    {
        $this->CustomerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }

    /**
     * Set contractorId
     *
     * @param integer $contractorId
     *
     * @return project
     */
    public function setContractorId($contractorId)
    {
        $this->ContractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId()
    {
        return $this->ContractorId;
    }

    /**
     * Set gipId
     *
     * @param integer $gipId
     *
     * @return project
     */
    public function setGipId($gipId)
    {
        $this->GipId = $gipId;

        return $this;
    }

    /**
     * Get gipId
     *
     * @return integer
     */
    public function getGipId()
    {
        return $this->GipId;
    }

    /**
     * Set nofot
     *
     * @param float $nofot
     *
     * @return project
     */
    public function setNofot($nofot)
    {
        $this->nofot = $nofot;

        return $this;
    }

    /**
     * Get nofot
     *
     * @return float
     */
    public function getNofot()
    {
        return $this->nofot;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return project
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\LegalEntity $customer
     *
     * @return project
     */
    public function setCustomer(\AppBundle\Entity\LegalEntity $customer = null)
    {
        $this->Customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \AppBundle\Entity\LegalEntity
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set contractor
     *
     * @param \AppBundle\Entity\LegalEntity $contractor
     *
     * @return project
     */
    public function setContractor(\AppBundle\Entity\LegalEntity $contractor = null)
    {
        $this->Contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return \AppBundle\Entity\LegalEntity
     */
    public function getContractor()
    {
        return $this->Contractor;
    }

    /**
     * Set gip
     *
     * @param \AppBundle\Entity\NaturPers $gip
     *
     * @return project
     */
    public function setGip(\AppBundle\Entity\NaturPers $gip = null)
    {
        $this->Gip = $gip;

        return $this;
    }

    /**
     * Get gip
     *
     * @return \AppBundle\Entity\NaturPers
     */
    public function getGip()
    {
        return $this->Gip;
    }

    /**
     * Add stage
     *
     * @param \AppBundle\Entity\ProjectStage $stage
     *
     * @return project
     */
    public function addStage(\AppBundle\Entity\ProjectStage $stage)
    {
        $this->stages[] = $stage;

        return $this;
    }

    /**
     * Remove stage
     *
     * @param \AppBundle\Entity\ProjectStage $stage
     */
    public function removeStage(\AppBundle\Entity\ProjectStage $stage)
    {
        $this->stages->removeElement($stage);
    }

    /**
     * Get stages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages()
    {
        return $this->stages;
    }
}
