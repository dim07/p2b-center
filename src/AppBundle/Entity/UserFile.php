<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserFile
 *
 * @ORM\Table(name="user_file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserFileRepository")
 */
class UserFile
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
     * @ORM\Column(name="id_user", type="integer")
     */
    private $idUser;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id_folio", type="integer")
     */
    private $idFolio;
    
    /**
     * @ORM\ManyToOne(targetEntity="portfolio", inversedBy="files")
     * @ORM\JoinColumn(name="id_folio", referencedColumnName="id")
     */
    private $folio;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="UserFileName", type="string", length=255)
     */
    private $userFileName;

    /**
     * @var bool
     *
     * @ORM\Column(name="IsImage", type="boolean")
     */
    private $isImage;

    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=6, nullable=true)
     */
    private $ext;


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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return UserFile
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return UserFile
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
     * Set userFileName
     *
     * @param string $userFileName
     *
     * @return UserFile
     */
    public function setUserFileName($userFileName)
    {
        $this->userFileName = $userFileName;

        return $this;
    }

    /**
     * Get userFileName
     *
     * @return string
     */
    public function getUserFileName()
    {
        return $this->userFileName;
    }

    /**
     * Set isImage
     *
     * @param boolean $isImage
     *
     * @return UserFile
     */
    public function setIsImage($isImage)
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * Get isImage
     *
     * @return bool
     */
    public function getIsImage()
    {
        return $this->isImage;
    }

    /**
     * Set ext
     *
     * @param string $ext
     *
     * @return UserFile
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set idFolio
     *
     * @param integer $idFolio
     *
     * @return UserFile
     */
    public function setIdFolio($idFolio)
    {
        $this->idFolio = $idFolio;

        return $this;
    }

    /**
     * Get idFolio
     *
     * @return integer
     */
    public function getIdFolio()
    {
        return $this->idFolio;
    }

    /**
     * Set folio
     *
     * @param \AppBundle\Entity\portfolio $folio
     *
     * @return UserFile
     */
    public function setFolio(\AppBundle\Entity\portfolio $folio = null)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio
     *
     * @return \AppBundle\Entity\portfolio
     */
    public function getFolio()
    {
        return $this->folio;
    }
}
