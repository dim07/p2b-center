<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $avatar;
    
    /**
     * @Vich\UploadableField(mapping="avatar_images", fileNameProperty="avatar")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;
    
     /**
     * 
     * @ORM\OneToOne(targetEntity="LegalEntity", mappedBy="user")
     */
    private $legentity;
    
     /**
     * 
     * @ORM\OneToOne(targetEntity="NaturPers", mappedBy="user")
     */
    private $pers;
    
    /**
     * @ORM\OneToMany(targetEntity="portfolio", mappedBy="user")
     */
    private $folios;

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        if($this->updatedAt) { 
            return $this->updatedAt;
        }
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

//         VERY IMPORTANT:
//         It is required that at least one field changes if you are using Doctrine,
//         otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function getavatar()
    {
        return $this->avatar;
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->folios = new ArrayCollection();
    }

//    public function getId()
//    {
//        return $this->id;
//    }
//
//    public function getUsername()
//    {
//        return $this->username;
//    }
//
//    /**
//     * @param string $username
//     */
//    public function setUsername($username)
//    {
//        $this->username = $username;
//    }
//
//    public function getEmail()
//    {
//        return $this->email;
//    }
//
//    /**
//     * @param string $email
//     */
//    public function setEmail($email)
//    {
//        $this->email = $email;
//    }
//
//    public function getPassword()
//    {
//        return $this->password;
//    }
//
//    /**
//     * @param string $password
//     */
//    public function setPassword($password)
//    {
//        $this->password = $password;
//    }
//
//    /**
//     * Returns the roles or permissions granted to the user for security.
//     */
//    public function getRoles()
//    {
//        $roles = $this->roles;
//
//        // guarantees that a user always has at least one role for security
//        if (empty($roles)) {
//            $roles[] = 'ROLE_USER';
//        }
//
//        return array_unique($roles);
//    }
//
//    public function setRoles(array $roles)
//    {
//        $this->roles = $roles;
//    }
//
//    /**
//     * Returns the salt that was originally used to encode the password.
//     *
//     * {@inheritdoc}
//     */
//    public function getSalt()
//    {
//        // See "Do you need to use a Salt?" at http://symfony.com/doc/current/cookbook/security/entity_provider.html
//        // we're using bcrypt in security.yml to encode the password, so
//        // the salt value is built-in and you don't have to generate one
//
//        return null;
//    }
//
//    /**
//     * Removes sensitive data from the user.
//     *
//     * {@inheritdoc}
//     */
//    public function eraseCredentials()
//    {
//        // if you had a plainPassword property, you'd nullify it here
//        // $this->plainPassword = null;
//    }

    /**
     * Set legentity
     *
     * @param \AppBundle\Entity\LegalEntity $legentity
     *
     * @return User
     */
    public function setLegentity(\AppBundle\Entity\LegalEntity $legentity = null)
    {
        $this->legentity = $legentity;

        return $this;
    }

    /**
     * Get legentity
     *
     * @return \AppBundle\Entity\LegalEntity
     */
    public function getLegentity()
    {
        return $this->legentity;
    }

    /**
     * Set pers
     *
     * @param \AppBundle\Entity\NaturPers $pers
     *
     * @return User
     */
    public function setPers(\AppBundle\Entity\NaturPers $pers = null)
    {
        $this->pers = $pers;

        return $this;
    }

    /**
     * Get pers
     *
     * @return \AppBundle\Entity\NaturPers
     */
    public function getPers()
    {
        return $this->pers;
    }

    /**
     * Add folio
     *
     * @param \AppBundle\Entity\portfolio $folio
     *
     * @return User
     */
    public function addFolio(\AppBundle\Entity\portfolio $folio)
    {
        $this->folios[] = $folio;

        return $this;
    }

    /**
     * Remove folio
     *
     * @param \AppBundle\Entity\portfolio $folio
     */
    public function removeFolio(\AppBundle\Entity\portfolio $folio)
    {
        $this->folios->removeElement($folio);
    }

    /**
     * Get folios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFolios()
    {
        return $this->folios;
    }
}
