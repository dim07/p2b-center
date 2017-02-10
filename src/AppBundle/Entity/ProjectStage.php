<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStage
 *
 * @ORM\Table(name="project_stage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectStageRepository")
 */
class ProjectStage
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
     * @ORM\Column(name="id_project", type="integer")
     */
    private $idProject;
    
    /**
     * @ORM\ManyToOne(targetEntity="project", inversedBy="stages")
     * @ORM\JoinColumn(name="id_project", referencedColumnName="id")
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EndDate", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float")
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;
    
     /**
     * @ORM\OneToMany(targetEntity="StageOrder", mappedBy="stage")
     */
    private $orders;
    
    public function __construct()
    {
        $this->orders = new ArrayCollection();
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
     * Set idProject
     *
     * @param integer $idProject
     *
     * @return ProjectStage
     */
    public function setIdProject($idProject)
    {
        $this->idProject = $idProject;

        return $this;
    }

    /**
     * Get idProject
     *
     * @return int
     */
    public function getIdProject()
    {
        return $this->idProject;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProjectStage
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
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return ProjectStage
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
     * Set cost
     *
     * @param float $cost
     *
     * @return ProjectStage
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
     * Set info
     *
     * @param string $info
     *
     * @return ProjectStage
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
     * Set project
     *
     * @param \AppBundle\Entity\project $project
     *
     * @return ProjectStage
     */
    public function setProject(\AppBundle\Entity\project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\StageOrder $order
     *
     * @return ProjectStage
     */
    public function addOrder(\AppBundle\Entity\StageOrder $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\StageOrder $order
     */
    public function removeOrder(\AppBundle\Entity\StageOrder $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
