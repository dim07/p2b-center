<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar')
            ->add('updatedAt')
            ->add('fio')
            ->add('hideName')
            ->add('phone')
            ->add('rating')
            ->add('region')
            ->add('agreement')
            ->add('groups', EntityType::class, array(
                'class' => 'AppBundle\Entity\Group',
                'choice_label' => 'id',

                'expanded' => true,
                'multiple' => true
            )) 
            ->add('sections', EntityType::class, array(
                'class' => 'AppBundle\Entity\Section',
                'choice_label' => 'name',

                'expanded' => true,
                'multiple' => true
            )) 
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
