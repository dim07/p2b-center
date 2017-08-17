<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\EventSubscriber;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
//use AppBundle\Form\Model\User;
use AppBundle\Entity\Sector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of AddObjectFieldSubscriber
 *
 * @author dima
 */
class AddObjectFieldSubscriber implements EventSubscriberInterface 
{
    private $factory;
 
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
 
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT     => 'preBind'
        );
    }
 
    private function addObjectForm($form, $object, $sector)
    {
        $form->add($this->factory->createNamed('object',EntityType::class, $object, array(
            'class'         => 'AppBundle:SectorObject',
            'empty_data'   => NULL,
            'auto_initialize' => FALSE,
            'mapped'        => false,
            'query_builder' => function (EntityRepository $repository) use ($sector) {
                $qb = $repository->createQueryBuilder('object')
                    ->innerJoin('object.sector', 'sector');
                if($sector instanceof Sector){
                    $qb->where('object.sector = :sector')
                    ->setParameter('sector', $sector);
                }elseif(is_numeric($sector)){
                    $qb->where('sector.id = :sector')
                    ->setParameter('sector', $sector);
                }else{
                    $qb->where('sector.name = :sector')
                    ->setParameter('sector', null);
                }
 
                return $qb;
            }
        )));
    }
 
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
 
        if (null === $data) {
            return;
        }
 
        $object = ($data->getObject()) ? $data->getObject() : null ;
        $sector = ($object) ? $object->getSector() : null ;
        $this->addObjectForm($form, $object, $sector);
    }
 
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
 
        if (null === $data) {
            return;
        }
 
        $object = array_key_exists('object', $data) ? $data['object'] : null;
        $sector = array_key_exists('sector', $data) ? $data['sector'] : null;
        $this->addObjectForm($form, $object, $sector);
    }
}
