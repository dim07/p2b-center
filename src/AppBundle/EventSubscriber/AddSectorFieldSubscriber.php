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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of AddSectorFieldSubscriber
 *
 * @author dima
 */
class AddSectorFieldSubscriber implements EventSubscriberInterface 
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
            FormEvents::POST_SUBMIT  => 'preBind'
        );
    }
 
    private function addSectorForm($form, $sector)
    {
        $form->add($this->factory->createNamed('sector', EntityType::class, $sector, array(
            'class'         => 'AppBundle:Sector',
            'mapped'        => false,
            'empty_data'   => NULL,
            'auto_initialize' => FALSE,
            'query_builder' => function (EntityRepository $repository) {
                $qb = $repository->createQueryBuilder('sector');
 
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
 
        $sector = ($data->getSubobject()) ? $data->getSubobject()->getObject()->getSector() : null ;
        $this->addSectorForm($form, $sector);
    }
 
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
 
        if (null === $data) {
            return;
        }
 
        $sector = array_key_exists('sector', $data) ? $data['sector'] : null;
        $this->addSectorForm($form, $sector);
    }
}
