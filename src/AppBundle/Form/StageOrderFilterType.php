<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class StageOrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('section', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Section',
                    'choice_label' => 'name',
                    'label' => 'Виды работ',
                    'group_by' => 'spec',
                    'multiple' => TRUE,
                    'attr'   =>  array(
                        'class'   => 'js-example-basic-multiple',
                        'style' => 'width: 100%'),
                    'required' => false
            ))     
            ->add('status',  ChoiceType::class, array(
                'label' => 'Статус',
                'apply_filter' => false, // disable filter
                'required' => false,
                'choices'  => array(
                    'Любые' => 0,
                    'Без исполнителя' => 1,
                    'В работе' => 2,
                    'Завершенные' => 3,
                ),
            ))
//                        ->add('startDate', Filters\DateRangeFilterType::class, [
//                'label' => 'Дата начала заказа',
////                'left_date_options'
//
//                
//            ])   
//            ->add('isPublic', Filters\BooleanFilterType::class)
                         
//            ->add('Contractor', Filters\EntityFilterType::class, array(
//                    'class' => 'AppBundle\Entity\LegalEntity',
//                    'label'    => 'Подрядчик',
//                    'choice_label' => 'name',
//                    'required' => false,
//                'attr'   =>  array(
//                    'class'   => 'js-example-basic-single',
//                    'style' => 'width: 100%')
//            )) 
//            ->add('UserIsp', Filters\EntityFilterType::class, array(
//                    'class' => 'AppBundle\Entity\User',
//                    'label'    => 'Исполнитель',
//                    'choice_label' => 'fio',
//                    'required' => false
//                    'attr'   =>  array(
//                    'class'   => 'js-example-basic-single'),
//            )) 
             
        ;
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
