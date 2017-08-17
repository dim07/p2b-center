<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * StageOrder
 *
 * @ORM\Table(name="stage_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StageOrderRepository")
 */
class StageOrder
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
     * @ORM\Column(name="id_section", type="integer", nullable=true)
     */
    private $idSection;
    
    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="orders")
     * @ORM\JoinColumn(name="id_section", referencedColumnName="id")
     */
    private $section;

    /**
     * @var int
     *
     * @ORM\Column(name="id_stage", type="integer")
     */
    private $idStage;

    /**
     * @var bool
     *
     * @ORM\Column(name="IsLegalEntity", type="boolean", nullable=true)
     */
    private $isLegalEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="after_order_id", type="integer", nullable=true)
     */
    private $AfterOrderId;
    
     /**
     * @ORM\ManyToOne(targetEntity="StageOrder", inversedBy="beforeorders")
     * @ORM\JoinColumn(name="after_order_id", referencedColumnName="id")
     */
    private $AfterOrder;
    
    /**
     * @ORM\OneToMany(targetEntity="StageOrder", mappedBy="AfterOrder")
     */
    private $beforeorders;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float", nullable=true)
     */
    private $cost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EndDate", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="StartDate", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var float
     *
     * @ORM\Column(name="FactCost", type="float", nullable=true)
     */
    private $factCost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FactEndDate", type="date", nullable=true)
     */
    private $factEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var bool
     *
     * @ORM\Column(name="IsPublic", type="boolean", nullable=true)
     */
    private $isPublic;
    
    /**
     * @ORM\ManyToOne(targetEntity="ProjectStage", inversedBy="orders")
     * @ORM\JoinColumn(name="id_stage", referencedColumnName="id")
     */
    private $stage;
    
    /**
     * @var int
     *
     * @ORM\Column(name="contractor_id", type="integer", nullable=true)
     */
    private $ContractorId;
    
    /**
     * @ORM\ManyToOne(targetEntity="LegalEntity", inversedBy="contractorders")
     * @ORM\JoinColumn(name="contractor_id", referencedColumnName="id")
     */
    private $Contractor;

    /**
     * @var int
     *
     * @ORM\Column(name="pers_id", type="integer", nullable=true)
     */
    private $UserId;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="pers_id", referencedColumnName="id")
     */
    private $UserIsp;

     /**
     * @ORM\OneToMany(targetEntity="OrderPay", mappedBy="order")
     * @ORM\OrderBy({"payDate" = "ASC"})
     */
    private $pays;
    
    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="order")
     * @ORM\OrderBy({"cost" = "ASC"})
     */
    private $offers;
    
    /**
     * @ORM\OneToMany(targetEntity="OrderTabel", mappedBy="order")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $tabels;
    
    /**
     * @var int
     *
     * @ORM\Column(name="offer_id", type="integer", nullable=true)
     */
    private $offerId;
    

//    private $offer;
    
    private $factPay;
    
    /**
     * @ORM\OneToMany(targetEntity="StageOrderEvent", mappedBy="order")
     */
    private $events;
    
    private $Project;
        
    private $Customer;    
    
    public function __construct()
    {
        $this->pays = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->beforeorders = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->tabels = new ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return StageOrder
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
    
    public function getOffer()
    {
        return $this->offer;
    }
    
    public function setOffer(\AppBundle\Entity\Offer $offer)
    {
        $this->offer = $offer;
        $user = $offer->getUser();
        $leg = $offer->getLegal();
        if (!is_null($user)) {
           $this->setUserIsp($user); 
        }
        if (!is_null($leg)) {
           $this->setContractor($leg); 
        }
        return $this;
    }
    
    public function getFactPay()
    {
        return $this->factPay;
    }
    
    public function setFactPay($FactPay)
    {
        $this->factPay = $FactPay;

        return $this;
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
     * Set idSection
     *
     * @param integer $idSection
     *
     * @return StageOrder
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
     * Set idStage
     *
     * @param integer $idStage
     *
     * @return StageOrder
     */
    public function setIdStage($idStage)
    {
        $this->idStage = $idStage;

        return $this;
    }

    /**
     * Get idStage
     *
     * @return int
     */
    public function getIdStage()
    {
        return $this->idStage;
    }

    /**
     * Set isLegalEntity
     *
     * @param boolean $isLegalEntity
     *
     * @return StageOrder
     */
    public function setIsLegalEntity($isLegalEntity)
    {
        $this->isLegalEntity = $isLegalEntity;

        return $this;
    }

    /**
     * Get isLegalEntity
     *
     * @return bool
     */
    public function getIsLegalEntity()
    {
        if (null !== $this->isLegalEntity) {
            return $this->isLegalEntity;
        } else {
            return FALSE;    
        }    
    }

    /**
     * Set cost
     *
     * @param float $cost
     *
     * @return StageOrder
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost()
    {
        return ($this->cost ? $this->cost : 0);
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return StageOrder
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return StageOrder
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set factCost
     *
     * @param float $factCost
     *
     * @return StageOrder
     */
    public function setFactCost($factCost)
    {
        $this->factCost = $factCost;

        return $this;
    }

    /**
     * Get factCost
     *
     * @return float
     */
    public function getFactCost()
    {
        return $this->factCost;
    }

    /**
     * Set factEndDate
     *
     * @param \DateTime $factEndDate
     *
     * @return StageOrder
     */
    public function setFactEndDate($factEndDate)
    {
        $this->factEndDate = $factEndDate;

        return $this;
    }

    /**
     * Get factEndDate
     *
     * @return \DateTime
     */
    public function getFactEndDate()
    {
        return $this->factEndDate;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return StageOrder
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
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return StageOrder
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set stage
     *
     * @param \AppBundle\Entity\ProjectStage $stage
     *
     * @return StageOrder
     */
    public function setStage(\AppBundle\Entity\ProjectStage $stage = null)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \AppBundle\Entity\ProjectStage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set afterOrderId
     *
     * @param integer $afterOrderId
     *
     * @return StageOrder
     */
    public function setAfterOrderId($afterOrderId)
    {
        $this->AfterOrderId = $afterOrderId;

        return $this;
    }

    /**
     * Get afterOrderId
     *
     * @return integer
     */
    public function getAfterOrderId()
    {
        return $this->AfterOrderId;
    }

    /**
     * Set contractorId
     *
     * @param integer $contractorId
     *
     * @return StageOrder
     */
    public function setContractorId($contractorId)
    {
        $this->ContractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId()
    {
        return $this->ContractorId;
    }

    /**
     * Set persId
     *
     * @param integer $persId
     *
     * @return StageOrder
     */
    public function setPersId($persId)
    {
        $this->PersId = $persId;

        return $this;
    }

    /**
     * Get persId
     *
     * @return integer
     */
    public function getPersId()
    {
        return $this->PersId;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return StageOrder
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

    /**
     * Set afterOrder
     *
     * @param \AppBundle\Entity\StageOrder $afterOrder
     *
     * @return StageOrder
     */
    public function setAfterOrder(\AppBundle\Entity\StageOrder $afterOrder = null)
    {
        $this->AfterOrder = $afterOrder;

        return $this;
    }

    /**
     * Get afterOrder
     *
     * @return \AppBundle\Entity\StageOrder
     */
    public function getAfterOrder()
    {
        return $this->AfterOrder;
    }

    /**
     * Set contractor
     *
     * @param \AppBundle\Entity\LegalEntity $contractor
     *
     * @return StageOrder
     */
    public function setContractor(\AppBundle\Entity\LegalEntity $contractor = null)
    {
        $this->Contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return \AppBundle\Entity\LegalEntity
     */
    public function getContractor()
    {
        return $this->Contractor;
    }

    /**
     * Set pers
     *
     * @param \AppBundle\Entity\NaturPers $pers
     *
     * @return StageOrder
     */
    public function setPers(\AppBundle\Entity\NaturPers $pers = null)
    {
        $this->Pers = $pers;

        return $this;
    }

    /**
     * Get pers
     *
     * @return \AppBundle\Entity\NaturPers
     */
    public function getPers()
    {
        return $this->Pers;
    }

    /**
     * Add pay
     *
     * @param \AppBundle\Entity\OrderPay $pay
     *
     * @return StageOrder
     */
    public function addPay(\AppBundle\Entity\OrderPay $pay)
    {
        $this->pays[] = $pay;

        return $this;
    }

    /**
     * Remove pay
     *
     * @param \AppBundle\Entity\OrderPay $pay
     */
    public function removePay(\AppBundle\Entity\OrderPay $pay)
    {
        $this->pays->removeElement($pay);
    }

    /**
     * Get pays
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\StageOrderEvent $event
     *
     * @return StageOrder
     */
    public function addEvent(\AppBundle\Entity\StageOrderEvent $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\StageOrderEvent $event
     */
    public function removeEvent(\AppBundle\Entity\StageOrderEvent $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return StageOrder
     */
    public function setUserId($userId)
    {
        $this->UserId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->UserId;
    }

    /**
     * Set userIsp
     *
     * @param \AppBundle\Entity\User $userIsp
     *
     * @return StageOrder
     */
    public function setUserIsp(\AppBundle\Entity\User $userIsp = null)
    {
        $this->UserIsp = $userIsp;

        return $this;
    }

    /**
     * Get userIsp
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserIsp()
    {
        return $this->UserIsp;
    }
    
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Add beforeorder
     *
     * @param \AppBundle\Entity\StageOrder $beforeorder
     *
     * @return StageOrder
     */
    public function addBeforeorder(\AppBundle\Entity\StageOrder $beforeorder)
    {
        $this->beforeorders[] = $beforeorder;

        return $this;
    }

    /**
     * Remove beforeorder
     *
     * @param \AppBundle\Entity\StageOrder $beforeorder
     */
    public function removeBeforeorder(\AppBundle\Entity\StageOrder $beforeorder)
    {
        $this->beforeorders->removeElement($beforeorder);
    }

    /**
     * Get beforeorders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBeforeorders()
    {
        return $this->beforeorders;
    }
    
    /**
     * Get Customer
     *
     * @return String
     */
    public function getCustomer()
    {
        return $this->stage->GetProject()->GetCustomer()->GetName();
    }
    
    public function getCustomerId()
    {
        return $this->stage->GetProject()->GetCustomer()->GetId();
    }
    
    /**
     * Get Project
     *
     * @return String
     */
    public function getProject()
    {
        return $this->stage->GetProject()->GetName();
    }
    
    public function getProjectId()
    {
        return $this->stage->GetProject()->GetId();
    }
    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (!$this->getIsLegalEntity() && null !== $this->getUserId() ) {
            if (!$this->getUserIsp()->getSections()->contains($this->getSection())) {
               $context->buildViolation('Выбранный исполнитель не имеет раздела специализации, который указан в заказе!')
                ->atPath('UserIsp')
                ->addViolation(); 
            }
        }
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     *
     * @return StageOrder
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;

        return $this;
    }

    /**
     * Get offerId
     *
     * @return integer
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * Add offer
     *
     * @param \AppBundle\Entity\Offer $offer
     *
     * @return StageOrder
     */
    public function addOffer(\AppBundle\Entity\Offer $offer)
    {
        $this->offers[] = $offer;

        return $this;
    }

    /**
     * Remove offer
     *
     * @param \AppBundle\Entity\Offer $offer
     */
    public function removeOffer(\AppBundle\Entity\Offer $offer)
    {
        $this->offers->removeElement($offer);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOffers()
    {
        return $this->offers;
    }
    
    /**
     * Add tabel
     *
     * @param \AppBundle\Entity\OrderTabel $tabel
     *
     * @return StageOrder
     */
    public function addTabel(\AppBundle\Entity\OrderTabel $tabel)
    {
        $this->tabels[] = $tabel;

        return $this;
    }

    /**
     * Remove tabel
     *
     * @param \AppBundle\Entity\OrderTabel $tabel
     */
    public function removeTabel(\AppBundle\Entity\OrderTabel $tabel)
    {
        $this->pays->removeElement($tabel);
    }

    /**
     * Get tabels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTabels()
    {
        return $this->tabels;
    }

    
    
    private $datas;
    public function getDatas()
    {
        return $this->datas;
    }
    
    public function setDatas($datas)
    {
        $this->datas = $datas;
        return $this;
    }
    
    public function getIspPays()
    {
        $cost = 0;
        if (!$this->getIsLegalEntity()) {
            foreach ($this->pays as $pay) {
                $cost += $pay->getFactPay();
            }    
        }    
        return $cost;
    }
}
