<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offer
 *
 * @ORM\Table(name="offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 */
class Offer
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
     * @ORM\ManyToOne(targetEntity="StageOrder", inversedBy="offers")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;
    
    /**
     * Отправитель
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="legal_id", type="integer", nullable=true)
     */
    private $legalId;
    
    /**
     * Отправитель
     * @ORM\ManyToOne(targetEntity="LegalEntity")
     * @ORM\JoinColumn(name="legal_id", referencedColumnName="id")
     */
    private $legal;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float")
     */
    private $cost;

    /**
     * @var int
     * @Assert\Range(
     *      min = 0,
     *      max = 255,
     *      minMessage = "Значение не может быть меньше {{ limit }}",
     *      maxMessage = "Значение не может быть больше {{ limit }}"
     * )
     * @ORM\Column(name="period", type="integer")
     */
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;


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
     * @return Offer
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Offer
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set legalId
     *
     * @param integer $legalId
     *
     * @return Offer
     */
    public function setLegalId($legalId)
    {
        $this->legalId = $legalId;

        return $this;
    }

    /**
     * Get legalId
     *
     * @return int
     */
    public function getLegalId()
    {
        return $this->legalId;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return Offer
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set period
     *
     * @param integer $period
     *
     * @return Offer
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return Offer
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
     * @return Offer
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Offer
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set legal
     *
     * @param \AppBundle\Entity\LegalEntity $legal
     *
     * @return Offer
     */
    public function setLegal(\AppBundle\Entity\LegalEntity $legal = null)
    {
        $this->legal = $legal;

        return $this;
    }

    /**
     * Get legal
     *
     * @return \AppBundle\Entity\LegalEntity
     */
    public function getLegal()
    {
        return $this->legal;
    }
    
    public function __toString() 
    {
        return 'Id:'.$this->getId().' '.$this->getUser().':'.$this->getCost().'р.,'.$this->getPeriod().'нед.';
    }
}
