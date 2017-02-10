<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StageOrder
 *
 * @ORM\Table(name="stage_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StageOrderRepository")
 */
class StageOrder
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
     * @ORM\Column(name="id_section", type="integer", nullable=true)
     */
    private $idSection;
    
    /**
     * @ORM\OneToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="id_section", referencedColumnName="id")
     */
    private $section;

    /**
     * @var int
     *
     * @ORM\Column(name="id_stage", type="integer")
     */
    private $idStage;

    /**
     * @var bool
     *
     * @ORM\Column(name="IsLegalEntity", type="boolean")
     */
    private $isLegalEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="after_order_id", type="integer", nullable=true)
     */
    private $AfterOrderId;
    
     /**
     * @ORM\OneToOne(targetEntity="StageOrder")
     * @ORM\JoinColumn(name="after_order_id", referencedColumnName="id")
     */
    private $AfterOrder;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float")
     */
    private $cost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EndDate", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="StartDate", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var float
     *
     * @ORM\Column(name="FactCost", type="float", nullable=true)
     */
    private $factCost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FactEndDate", type="date", nullable=true)
     */
    private $factEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var bool
     *
     * @ORM\Column(name="IsPublic", type="boolean", nullable=true)
     */
    private $isPublic;
    
    /**
     * @ORM\ManyToOne(targetEntity="ProjectStage", inversedBy="orders")
     * @ORM\JoinColumn(name="id_stage", referencedColumnName="id")
     */
    private $stage;
    
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
     * @ORM\Column(name="pers_id", type="integer", nullable=true)
     */
    private $PersId;
    
    /**
     * @ORM\OneToOne(targetEntity="NaturPers")
     * @ORM\JoinColumn(name="pers_id", referencedColumnName="id")
     */
    private $Pers;

     /**
     * @ORM\OneToMany(targetEntity="OrderPay", mappedBy="order")
     */
    private $pays;
    
    /**
     * @ORM\OneToMany(targetEntity="StageOrderEvent", mappedBy="order")
     */
    private $events;
    
    public function __construct()
    {
        $this->pays = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     * Set idSection
     *
     * @param integer $idSection
     *
     * @return StageOrder
     */
    public function setIdSection($idSection)
    {
        $this->idSection = $idSection;

        return $this;
    }

    /**
     * Get idSection
     *
     * @return int
     */
    public function getIdSection()
    {
        return $this->idSection;
    }

    /**
     * Set idStage
     *
     * @param integer $idStage
     *
     * @return StageOrder
     */
    public function setIdStage($idStage)
    {
        $this->idStage = $idStage;

        return $this;
    }

    /**
     * Get idStage
     *
     * @return int
     */
    public function getIdStage()
    {
        return $this->idStage;
    }

    /**
     * Set isLegalEntity
     *
     * @param boolean $isLegalEntity
     *
     * @return StageOrder
     */
    public function setIsLegalEntity($isLegalEntity)
    {
        $this->isLegalEntity = $isLegalEntity;

        return $this;
    }

    /**
     * Get isLegalEntity
     *
     * @return bool
     */
    public function getIsLegalEntity()
    {
        return $this->isLegalEntity;
    }

    /**
     * Set cost
     *
     * @param float $cost
     *
     * @return StageOrder
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return StageOrder
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return StageOrder
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set factCost
     *
     * @param float $factCost
     *
     * @return StageOrder
     */
    public function setFactCost($factCost)
    {
        $this->factCost = $factCost;

        return $this;
    }

    /**
     * Get factCost
     *
     * @return float
     */
    public function getFactCost()
    {
        return $this->factCost;
    }

    /**
     * Set factEndDate
     *
     * @param \DateTime $factEndDate
     *
     * @return StageOrder
     */
    public function setFactEndDate($factEndDate)
    {
        $this->factEndDate = $factEndDate;

        return $this;
    }

    /**
     * Get factEndDate
     *
     * @return \DateTime
     */
    public function getFactEndDate()
    {
        return $this->factEndDate;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return StageOrder
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
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return StageOrder
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set stage
     *
     * @param \AppBundle\Entity\ProjectStage $stage
     *
     * @return StageOrder
     */
    public function setStage(\AppBundle\Entity\ProjectStage $stage = null)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \AppBundle\Entity\ProjectStage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set afterOrderId
     *
     * @param integer $afterOrderId
     *
     * @return StageOrder
     */
    public function setAfterOrderId($afterOrderId)
    {
        $this->AfterOrderId = $afterOrderId;

        return $this;
    }

    /**
     * Get afterOrderId
     *
     * @return integer
     */
    public function getAfterOrderId()
    {
        return $this->AfterOrderId;
    }

    /**
     * Set contractorId
     *
     * @param integer $contractorId
     *
     * @return StageOrder
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
     * Set persId
     *
     * @param integer $persId
     *
     * @return StageOrder
     */
    public function setPersId($persId)
    {
        $this->PersId = $persId;

        return $this;
    }

    /**
     * Get persId
     *
     * @return integer
     */
    public function getPersId()
    {
        return $this->PersId;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return StageOrder
     */
    public function setSection(\AppBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AppBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set afterOrder
     *
     * @param \AppBundle\Entity\StageOrder $afterOrder
     *
     * @return StageOrder
     */
    public function setAfterOrder(\AppBundle\Entity\StageOrder $afterOrder = null)
    {
        $this->AfterOrder = $afterOrder;

        return $this;
    }

    /**
     * Get afterOrder
     *
     * @return \AppBundle\Entity\StageOrder
     */
    public function getAfterOrder()
    {
        return $this->AfterOrder;
    }

    /**
     * Set contractor
     *
     * @param \AppBundle\Entity\LegalEntity $contractor
     *
     * @return StageOrder
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
     * Set pers
     *
     * @param \AppBundle\Entity\NaturPers $pers
     *
     * @return StageOrder
     */
    public function setPers(\AppBundle\Entity\NaturPers $pers = null)
    {
        $this->Pers = $pers;

        return $this;
    }

    /**
     * Get pers
     *
     * @return \AppBundle\Entity\NaturPers
     */
    public function getPers()
    {
        return $this->Pers;
    }

    /**
     * Add pay
     *
     * @param \AppBundle\Entity\OrderPay $pay
     *
     * @return StageOrder
     */
    public function addPay(\AppBundle\Entity\OrderPay $pay)
    {
        $this->pays[] = $pay;

        return $this;
    }

    /**
     * Remove pay
     *
     * @param \AppBundle\Entity\OrderPay $pay
     */
    public function removePay(\AppBundle\Entity\OrderPay $pay)
    {
        $this->pays->removeElement($pay);
    }

    /**
     * Get pays
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\StageOrderEvent $event
     *
     * @return StageOrder
     */
    public function addEvent(\AppBundle\Entity\StageOrderEvent $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\StageOrderEvent $event
     */
    public function removeEvent(\AppBundle\Entity\StageOrderEvent $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
