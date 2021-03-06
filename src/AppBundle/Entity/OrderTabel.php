<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderTabel
 *
 * @ORM\Table(name="order_tabel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderTabelRepository")
 */
class OrderTabel
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
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;
    
    /**
     * @ORM\ManyToOne(targetEntity="StageOrder", inversedBy="tabels")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var int
     *
     * @ORM\Column(name="clock", type="integer")
     */
    private $clock;


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
     * Set orderId
     *
     * @param integer $orderId
     *
     * @return OrderTabel
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return OrderTabel
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return OrderTabel
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
     * Set clock
     *
     * @param integer $clock
     *
     * @return OrderTabel
     */
    public function setClock($clock)
    {
        $this->clock = $clock;

        return $this;
    }

    /**
     * Get clock
     *
     * @return int
     */
    public function getClock()
    {
        return $this->clock;
    }
    
    /**
     * Set order
     *
     * @param \AppBundle\Entity\StageOrder $order
     *
     * @return StageOrderTabel
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
}
