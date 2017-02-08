<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NaturPers
 *
 * @ORM\Table(name="natur_pers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NaturPersRepository")
 */
class NaturPers
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
     * @ORM\Column(name="fio", type="string", length=100)
     */
    private $fio;

    /**
     * @var bool
     *
     * @ORM\Column(name="HideName", type="boolean")
     */
    private $hideName;

    /**
     * @var int
     *
     * @ORM\Column(name="id_spec", type="integer", nullable=true)
     */
    private $idSpec;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=100, nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="agreement", type="string", length=255, nullable=true)
     */
    private $agreement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;
    
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
     * Set fio
     *
     * @param string $fio
     *
     * @return NaturPers
     */
    public function setFio($fio)
    {
        $this->fio = $fio;

        return $this;
    }

    /**
     * Get fio
     *
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * Set hideName
     *
     * @param boolean $hideName
     *
     * @return NaturPers
     */
    public function setHideName($hideName)
    {
        $this->hideName = $hideName;

        return $this;
    }

    /**
     * Get hideName
     *
     * @return bool
     */
    public function getHideName()
    {
        return $this->hideName;
    }

    /**
     * Set idSpec
     *
     * @param integer $idSpec
     *
     * @return NaturPers
     */
    public function setIdSpec($idSpec)
    {
        $this->idSpec = $idSpec;

        return $this;
    }

    /**
     * Get idSpec
     *
     * @return int
     */
    public function getIdSpec()
    {
        return $this->idSpec;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return NaturPers
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return NaturPers
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return NaturPers
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set agreement
     *
     * @param string $agreement
     *
     * @return NaturPers
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return string
     */
    public function getAgreement()
    {
        return $this->agreement;
    }
    
    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return LegalEntity
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }
}

