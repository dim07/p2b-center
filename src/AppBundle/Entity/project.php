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
     * @ORM\Column(name="customer", type="integer")
     */
    private $customer;

    /**
     * @var int
     *
     * @ORM\Column(name="contractor", type="integer", nullable=true)
     */
    private $contractor;

    /**
     * @var int
     *
     * @ORM\Column(name="gip", type="integer", nullable=true)
     */
    private $gip;

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
     * Get id
     *
     * @return int
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
     * Set customer
     *
     * @param integer $customer
     *
     * @return project
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set contractor
     *
     * @param integer $contractor
     *
     * @return project
     */
    public function setContractor($contractor)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return int
     */
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * Set gip
     *
     * @param integer $gip
     *
     * @return project
     */
    public function setGip($gip)
    {
        $this->gip = $gip;

        return $this;
    }

    /**
     * Get gip
     *
     * @return int
     */
    public function getGip()
    {
        return $this->gip;
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
}

