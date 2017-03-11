<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class OrderPayFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('idOrder', Filters\NumberFilterType::class)
            ->add('planPay', Filters\NumberFilterType::class)
            ->add('factPay', Filters\NumberFilterType::class)
            ->add('chargDate', Filters\DateFilterType::class)
            ->add('payDate', Filters\DateFilterType::class)
            ->add('statementDate', Filters\DateFilterType::class)
        
            ->add('order', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\StageOrder',
                    'choice_label' => 'id',
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
