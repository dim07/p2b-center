<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
//            ->add('fio', Filters\TextFilterType::class)
//            ->add('phone', Filters\TextFilterType::class)
            ->add('rating', Filters\NumberFilterType::class)
            ->add('region', Filters\TextFilterType::class)
        
            ->add('sections', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Section',
                    'choice_label' => 'name',
                    'label' => 'Разделы специализаций',
                    'group_by' => 'spec',
                    'multiple' => TRUE,
                    'attr'   =>  array(
                        'class'   => 'js-example-basic-multiple',
                        'style' => 'width: 100%'),
                    'required' => false
            )) 
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
