<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class projectFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('name', Filters\TextFilterType::class)
            ->add('CustomerId', Filters\NumberFilterType::class)
            ->add('ContractorId', Filters\NumberFilterType::class)
            ->add('GipId', Filters\NumberFilterType::class)
            ->add('nofot', Filters\NumberFilterType::class)
            ->add('info', Filters\TextFilterType::class)
        
            ->add('Customer', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\LegalEntity',
                    'choice_label' => 'name',
            )) 
            ->add('Contractor', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\LegalEntity',
                    'choice_label' => 'name',
            )) 
            ->add('gip', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\User',
                    'choice_label' => 'avatar',
            )) 
            ->add('stages', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\ProjectStage',
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
