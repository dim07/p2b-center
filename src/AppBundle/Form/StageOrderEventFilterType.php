<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class StageOrderEventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('idOrder', Filters\NumberFilterType::class)
            ->add('idEventType', Filters\NumberFilterType::class)
            ->add('eventDate', Filters\DateTimeFilterType::class)
            ->add('info', Filters\TextFilterType::class)
            ->add('idFile', Filters\NumberFilterType::class)
        
            ->add('order', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\StageOrder',
                    'choice_label' => 'id',
            )) 
            ->add('File', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\UserFile',
                    'choice_label' => 'name',
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
