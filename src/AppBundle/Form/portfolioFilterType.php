<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class portfolioFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('idUser', Filters\NumberFilterType::class)
            ->add('name', Filters\TextFilterType::class)
            ->add('idSection', Filters\NumberFilterType::class)
            ->add('workDate', Filters\DateFilterType::class)
            ->add('info', Filters\TextFilterType::class)
        
            ->add('user', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\User',
                    'choice_label' => 'avatar',
            )) 
            ->add('section', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Section',
                    'choice_label' => 'name',
            )) 
            ->add('files', Filters\EntityFilterType::class, array(
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
