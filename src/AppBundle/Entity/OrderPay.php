<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderPay
 *
 * @ORM\Table(name="order_pay")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderPayRepository")
 */
class OrderPay
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
     * @var float
     *
     * @ORM\Column(name="PlanPay", type="float")
     */
    private $planPay;

    /**
     * @var float
     *
     * @ORM\Column(name="FactPay", type="float", nullable=true)
     */
    private $factPay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ChargDate", type="date")
     */
    private $chargDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="PayDate", type="date", nullable=true)
     */
    private $payDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="StatementDate", type="date", nullable=true)
     */
    private $statementDate;


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
     * @return OrderPay
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
     * Set planPay
     *
     * @param float $planPay
     *
     * @return OrderPay
     */
    public function setPlanPay($planPay)
    {
        $this->planPay = $planPay;

        return $this;
    }

    /**
     * Get planPay
     *
     * @return float
     */
    public function getPlanPay()
    {
        return $this->planPay;
    }

    /**
     * Set factPay
     *
     * @param float $factPay
     *
     * @return OrderPay
     */
    public function setFactPay($factPay)
    {
        $this->factPay = $factPay;

        return $this;
    }

    /**
     * Get factPay
     *
     * @return float
     */
    public function getFactPay()
    {
        return $this->factPay;
    }

    /**
     * Set chargDate
     *
     * @param \DateTime $chargDate
     *
     * @return OrderPay
     */
    public function setChargDate($chargDate)
    {
        $this->chargDate = $chargDate;

        return $this;
    }

    /**
     * Get chargDate
     *
     * @return \DateTime
     */
    public function getChargDate()
    {
        return $this->chargDate;
    }

    /**
     * Set payDate
     *
     * @param \DateTime $payDate
     *
     * @return OrderPay
     */
    public function setPayDate($payDate)
    {
        $this->payDate = $payDate;

        return $this;
    }

    /**
     * Get payDate
     *
     * @return \DateTime
     */
    public function getPayDate()
    {
        return $this->payDate;
    }

    /**
     * Set statementDate
     *
     * @param \DateTime $statementDate
     *
     * @return OrderPay
     */
    public function setStatementDate($statementDate)
    {
        $this->statementDate = $statementDate;

        return $this;
    }

    /**
     * Get statementDate
     *
     * @return \DateTime
     */
    public function getStatementDate()
    {
        return $this->statementDate;
    }
}

