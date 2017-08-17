<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToOne(targetEntity="LegalEntity", inversedBy="customprojects")
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
     * @ORM\ManyToOne(targetEntity="LegalEntity", inversedBy="contractprojects")
     * @ORM\JoinColumn(name="contractor_id", referencedColumnName="id")
     */
    private $Contractor;

    /**
     * @var float
     *
     * @ORM\Column(name="nofot", type="float", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 1,
     *      minMessage = "Значение не может быть меньше {{ limit }}",
     *      maxMessage = "Значение не может быть больше {{ limit }}"
     * )
     */
    private $nofot;

     /**
     * @ORM\OneToMany(targetEntity="ProjectStage", mappedBy="project")
     * @ORM\OrderBy({"num" = "ASC"})
     */
    private $stages;
    
        /**
     * @var int
     *
     * @ORM\Column(name="gip_id", type="integer", nullable=true)
     */
    private $GipId;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gipprojects")
     * @ORM\JoinColumn(name="gip_id", referencedColumnName="id")
     */
    private $gip;
    
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
     * Set gip
     *
     * @param \AppBundle\Entity\User $gip
     *
     * @return project
     */
    public function setGip(\AppBundle\Entity\User $gip = null)
    {
        $this->gip = $gip;

        return $this;
    }

    /**
     * Get gip
     *
     * @return \AppBundle\Entity\User
     */
    public function getGip()
    {
        return $this->gip;
    }
    
    public function __toString() 
    {
        return $this->getName();
    }
    
    private $datas;
    public function getDatas()
    {
        return $this->datas;
    }
    
    public function setDatas($datas)
    {
        $this->datas = $datas;
        return $this;
    }
    
    private $datas1;
    public function getDatas1()
    {
        return $this->datas1;
    }
    
    public function setDatas1($datas)
    {
        $this->datas1 = $datas;
        return $this;
    }
    
    public function getPlanCost()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
            $cost += $stage->getCost();
        }
        return $cost;
    }
    
    public function getFactCost()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
            $fd = $stage->getFactEndDate();
            if (!($fd === NULL)) {
                $cost += $stage->getCost();
            }    
        }
        return $cost;
    }
    
    public function getSubCost()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
            $cost += $stage->getSubCost();
        }
        return $cost;
    }
    
    public function getSubFactCost()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
            $cost += $stage->getSubFactCost();
        }
        return $cost;
    }
    
    public function getIspCost()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
            $cost += $stage->getIspCost();
        }
        return $cost;
    }
    
    public function getIspPays()
    {
        $cost = 0;
        foreach ($this->stages as $stage) {
                $cost += $stage->getIspPays();
            }    
        return $cost;
    }
}
