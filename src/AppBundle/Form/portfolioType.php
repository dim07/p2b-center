<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\UserFileType;
//use AppBundle\Form\Type\SubObjectType;
//use Doctrine\ORM\EntityRepository;
//use AppBundle\EventSubscriber\AddSubobjectFieldSubscriber;
//use AppBundle\EventSubscriber\AddObjectFieldSubscriber;
//use AppBundle\EventSubscriber\AddSectorFieldSubscriber;

class portfolioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $portfolio = $builder->getData();
        $objects = $portfolio->getSector()->getObjects();
        $builder
            ->add('name', null, array('label' => 'Наименование', 'required' => true,))
            ->add('workDate', DateTimePickerType::class, [
                'label' => 'Дата работы',
                'format' => 'dd.MM.yyyy',
                'required' => true,
            ])
            ->add('section', EntityType::class, array(
                'class' => 'AppBundle\Entity\Section',
                'choice_label' => 'name',
                'group_by' => 'spec',
                'multiple' => false,
                'label' => 'Раздел специализации',
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'empty_data' => null,
                'required' => false,
            ))  
//            ->add('sector', EntityType::class, array(
//                'class' => 'AppBundle\Entity\Sector',
//                'choice_label' => 'name',
//                'multiple' => false,
//                'label' => 'Отрасль',
//                'empty_data' => null,
//                'required' => false,
//                'disabled' => true,
//            ))    
            ->add('sector', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array('required' => false,'disabled' => true))    
            ->add('object', EntityType::class, array(
                'class' => 'AppBundle\Entity\SectorObject',
                'choice_label' => 'name',
                'multiple' => false,
                'label' => 'Объект отрасли',
//                'attr'   =>  array(
//                    'class'   => 'js-example-basic-single',
//                    'style' => 'width: 100%'),
                'empty_data' => null,
                'required' => false,
                'choices'     => $objects,
            ));

        
        $frmModifierSubobject = function ( FormInterface $form, $object = null ) {
            $subobjects = null === $object ? array() : $object->getSubobjects();

            $form->add('subobject', EntityType::class, array(
                'label'       => 'Подобъект отрасли',
                'class'           => 'AppBundle:SubObject',
                'placeholder'     => '',
                'auto_initialize' => false,
//                'attr'            => array(
//                    'class'   => 'js-example-basic-single',
//                    'style' => 'width: 100%'
//                ),
                'empty_data' => null,
                'required' => false,
//                'disabled' => true,
                'disabled' => ( null === $object ) ? true : false,
                'choices'     => $subobjects,
            ));
        };
        
//        $factory = $builder->getFormFactory();
//        $subobjectSubscriber = new AddSubobjectFieldSubscriber($factory);
//        $builder->addEventSubscriber($subobjectSubscriber);
//        $objectSubscriber = new AddObjectFieldSubscriber($factory);
//        $builder->addEventSubscriber($objectSubscriber);
//        $sectorSubscriber = new AddSectorFieldSubscriber($factory);
//        $builder->addEventSubscriber($sectorSubscriber);
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($frmModifierSubobject) {
                $data = $event->getData();
                $frm = $event->getForm();
              
                $frmModifierSubobject($frm , $data->getObject());
            }
        );

        $builder->get('object')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($frmModifierSubobject) {
                $object = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $frmModifierSubobject($event->getForm()->getParent(), $object);
            }
        );
        
//        $factory = $builder->getFormFactory();
//        $subobjectSubscriber = new AddSubobjectFieldSubscriber($factory);
//        $builder->get('object')->addEventSubscriber($subobjectSubscriber);

//        $builder->get('object')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($frmModifierSubobject) {
//                // It's important here to fetch $event->getForm()->getData(), as
//                // $event->getData() will get you the client data (that is, the ID)
//                $object = $event->getForm()->getData();
//
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback functions!
//                $frmModifierSubobject($event->getForm()->getParent(), $object);
//            }
//        );
        
        $builder
            ->add('info', TextareaType::class, array(
                'label' => 'Описание', 
                'attr' => array('rows' => '4'),
                'required' => false
            ))
            ->add('files', CollectionType::class, array(
                'entry_type' => UserFileType::class,
                'label' => false,
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))    
            ;    
        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\portfolio'
        ));
    }
}
