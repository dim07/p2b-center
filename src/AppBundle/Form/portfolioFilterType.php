<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class portfolioFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class, array(
                'label' => 'ID работы'
            ))
            ->add('idUser', Filters\NumberFilterType::class, array(
                'label' => 'ID исполнителя'
            ))
            ->add('name', Filters\TextFilterType::class, array(
                'label' => 'Наименование работы (поиск по вхождению)'
            ))
            ->add('section', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Section',
                    'choice_label' => 'name',
                    'label' => 'Раздел специализации',
                    'group_by' => 'spec',
                    'multiple' => false,
                    'attr'   =>  array(
                        'class'   => 'js-example-basic-single',
                        'style' => 'width: 100%'),
                    'required' => false
            ))
            ->add('info', Filters\TextFilterType::class, array(
                'label' => 'Описание работы (поиск по вхождению)'
            ))
            ->add('sector', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Sector',
                    'choice_label' => 'name',
                    'label' => 'Отрасль',
                    'multiple' => false,
                    'required' => false
            ))
            ->add('object', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\SectorObject',
                    'choice_label' => 'name',
                    'label' => 'Объект отрасли',
                    'multiple' => false,
                    'required' => false
            ))
            ->add('subobject', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\SubObject',
                    'choice_label' => 'name',
                    'label' => 'Подобъект отрасли',
                    'multiple' => false,
                    'required' => false
            ))    
 
        ;
        
//        $frmModifierObject = function ( FormInterface $form, $sector = null ) {
//            $objects = null === $sector ? array() : $sector->getObjects();
//
//            $form->add('object', Filters\EntityFilterType::class, array(
//                'label'       => 'Oбъект отрасли',
//                'class'           => 'AppBundle:SectorObject',
//                'placeholder'     => '',
//                'auto_initialize' => false,
//                'empty_data' => null,
//                'required' => false,
////                'disabled' => ( null === $sector ) ? true : false,
//                'choices'     => $objects,
//            ));
//        };
        
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($frmModifierObject) {
//                $data = $event->getData();
//                $frm = $event->getForm();
//              
//                $frmModifierObject($frm , $data['sector']);
//            }
//        );
//
//        $builder->get('sector')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($frmModifierObject) {
//                $sector = $event->getForm()->getData();
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback functions!
//                $frmModifierObject($event->getForm()->getParent(), $sector);
//            }
//        );
        
        $builder->setMethod("GET");


    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
