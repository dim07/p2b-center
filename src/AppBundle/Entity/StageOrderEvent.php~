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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EventType")
     * @ORM\JoinColumn(name="id_event_type", referencedColumnName="id")
     */
    private $EventType;
    
    

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
    * @ORM\Column(name="file",type="string", length=255, nullable=true)
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
     * Set eventType
     *
     * @param \EventType $eventType
     *
     * @return StageOrderEvent
     */
    public function setEventType(\AppBundle\Entity\EventType $eventType)
    {
        $this->EventType = $eventType;

        return $this;
    }

    /**
     * Get eventType
     *
     * @return \EventType
     */
    public function getEventType()
    {
        return $this->EventType;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return StageOrderEvent
     */
    public function setFile($file)
    {
        $this->File = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->File;
    }
}
