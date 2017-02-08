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
     * @ORM\Column(name="AfterOrder", type="integer", nullable=true)
     */
    private $afterOrder;

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
     * Set afterOrder
     *
     * @param integer $afterOrder
     *
     * @return StageOrder
     */
    public function setAfterOrder($afterOrder)
    {
        $this->afterOrder = $afterOrder;

        return $this;
    }

    /**
     * Get afterOrder
     *
     * @return int
     */
    public function getAfterOrder()
    {
        return $this->afterOrder;
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
}

