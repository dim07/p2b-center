<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class OfferFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('orderId', Filters\NumberFilterType::class)
            ->add('userId', Filters\NumberFilterType::class)
            ->add('legalId', Filters\NumberFilterType::class)
            ->add('cost', Filters\NumberFilterType::class)
            ->add('period', Filters\NumberFilterType::class)
            ->add('info', Filters\TextFilterType::class)
        
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
