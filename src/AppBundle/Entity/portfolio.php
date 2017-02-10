<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * portfolio
 *
 * @ORM\Table(name="portfolio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\portfolioRepository")
 */
class portfolio
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="folios")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="id_section", type="integer")
     */
    private $idSection;
    
    /**
     * @ORM\OneToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="id_section", referencedColumnName="id")
     */
    private $section;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WorkDate", type="date", nullable=true)
     */
    private $workDate;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;
    
    /**
     * @ORM\OneToMany(targetEntity="UserFile", mappedBy="folio")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return portfolio
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
     * @return portfolio
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
     * Set idSection
     *
     * @param integer $idSection
     *
     * @return portfolio
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
     * Set workDate
     *
     * @param \DateTime $workDate
     *
     * @return portfolio
     */
    public function setWorkDate($workDate)
    {
        $this->workDate = $workDate;

        return $this;
    }

    /**
     * Get workDate
     *
     * @return \DateTime
     */
    public function getWorkDate()
    {
        return $this->workDate;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return portfolio
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
     * Add file
     *
     * @param \AppBundle\Entity\UserFile $file
     *
     * @return portfolio
     */
    public function addFile(\AppBundle\Entity\UserFile $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \AppBundle\Entity\UserFile $file
     */
    public function removeFile(\AppBundle\Entity\UserFile $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return portfolio
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
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return portfolio
     */
    public function setSection(\AppBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AppBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }
}
