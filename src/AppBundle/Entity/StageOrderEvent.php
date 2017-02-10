<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StageOrderEvent
 *
 * @ORM\Table(name="stage_order_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StageOrderEventRepository")
 */
class StageOrderEvent
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
     * @ORM\Column(name="id_order", type="integer")
     */
    private $idOrder;
    
    /**
     * @ORM\ManyToOne(targetEntity="StageOrder", inversedBy="events")
     * @ORM\JoinColumn(name="id_order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var int
     *
     * @ORM\Column(name="id_event_type", type="integer")
     */
    private $idEventType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EventDate", type="datetime")
     */
    private $eventDate;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var int
     *
     * @ORM\Column(name="id_file", type="integer", nullable=true)
     */
    private $idFile;
    
    /**
     * @ORM\OneToOne(targetEntity="UserFile")
     * @ORM\JoinColumn(name="id_file", referencedColumnName="id")
     */
    private $File;


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
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return StageOrderEvent
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return int
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idEventType
     *
     * @param integer $idEventType
     *
     * @return StageOrderEvent
     */
    public function setIdEventType($idEventType)
    {
        $this->idEventType = $idEventType;

        return $this;
    }

    /**
     * Get idEventType
     *
     * @return int
     */
    public function getIdEventType()
    {
        return $this->idEventType;
    }

    /**
     * Set eventDate
     *
     * @param \DateTime $eventDate
     *
     * @return StageOrderEvent
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * Get eventDate
     *
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return StageOrderEvent
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
     * Set idFile
     *
     * @param integer $idFile
     *
     * @return StageOrderEvent
     */
    public function setIdFile($idFile)
    {
        $this->idFile = $idFile;

        return $this;
    }

    /**
     * Get idFile
     *
     * @return int
     */
    public function getIdFile()
    {
        return $this->idFile;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\StageOrder $order
     *
     * @return StageOrderEvent
     */
    public function setOrder(\AppBundle\Entity\StageOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\StageOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set file
     *
     * @param \AppBundle\Entity\UserFile $file
     *
     * @return StageOrderEvent
     */
    public function setFile(\AppBundle\Entity\UserFile $file = null)
    {
        $this->File = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \AppBundle\Entity\UserFile
     */
    public function getFile()
    {
        return $this->File;
    }
}
