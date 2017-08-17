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
use AppBundle\Entity\SectorObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of AddSubojectFieldSubscriber
 *
 * @author dima
 */
class AddSubobjectFieldSubscriber  implements EventSubscriberInterface
{
    private $factory;
 
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
 
    public static function getSubscribedEvents()
    {
        return array(
//            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT     => 'postSubmit'
        );
    }
 
    private function addSubobjectForm($form, $object)
    {
//        $form->add($this->factory->createNamed('subobject',EntityType::class, null, array(
//            'class'         => 'AppBundle:Subobject',
//            'empty_data'   => null,
//            'auto_initialize' => FALSE,
//            'query_builder' => function (EntityRepository $repository) use ($object) {
//                $qb = $repository->createQueryBuilder('subobject')
//                    ->innerJoin('subobject.object', 'object');
//                if ($object instanceof SectorObject) {
//                    $qb->where('subobject.object = :object')
//                    ->setParameter('object', $object);
//                } elseif (is_numeric($object)) {
//                    $qb->where('object.id = :object')
//                    ->setParameter('object', $object);
//                } else {
//                    $qb->where('object.name = :object')
//                    ->setParameter('object', null);
//                }
// 
//                return $qb;
//            }
//        )));
        $subobjects = null === $object ? array() : $object->getSubobjects();
        $form->add('subobject', EntityType::class, array(
                'label'       => 'Подобъект отрасли',
                'class'           => 'AppBundle:SubObject',
                'placeholder'     => '',
                'auto_initialize' => false,
                'attr'            => array(
//                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'
                ),
                'empty_data' => null,
                'required' => false,
                'disabled' => ( null === $object ) ? true : false,
                'choices'     => $subobjects,
            ));
    }
 
//    public function preSetData(FormEvent $event)
//    {
//        $data = $event->getData();
//        $form = $event->getForm();
// 
//        if (null === $data) {
//            return;
//        }
// 
//        $object = ($data->getSubobject()) ? $data->getSubobject()->getObject() : null ;
//        $this->addSubobjectForm($form, $object);
//    }
 
    public function postSubmit(FormEvent $event)
    {
//        $data = $event->getData();
//        $form = $event->getForm();
// 
//        if (null === $data) {
//            return;
//        }
// 
//        $object = array_key_exists('object', $data) ? $data['object'] : null;
//        $this->addSubobjectForm($form, $object);
        
        $frmModifierSubobject = function ( FormInterface $form, $object = null ) {
            $subobjects = null === $object ? array() : $object->getSubobjects();

            $form->add('subobject', EntityType::class, array(
                'label'       => 'Подобъект отрасли',
                'class'           => 'AppBundle:SubObject',
                'placeholder'     => '',
                'auto_initialize' => false,
                'attr'            => array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'
                ),
                'empty_data' => null,
                'required' => false,
                'disabled' => false,//( null === $object ) ? true : false,
                'choices'     => $subobjects,
            ));
        };
        $object = $event->getForm()->getData();
        $frmModifierSubobject($event->getForm()->getParent(), $object);
    }
}
