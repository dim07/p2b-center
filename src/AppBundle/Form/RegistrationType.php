<?php
// src/AppBundle/Form/RegistrationType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fio', null, array('label' => 'ФИО',))
            ->add('hideName', null, array('label' => 'Скрывать ФИО',))  
            ->add('phone', null, array('label' => 'Телефон',))     
            ->add('sections', EntityType::class, array(
                'class' => 'AppBundle\Entity\Section',
                'choice_label' => 'name',
                'group_by' => 'spec',
//                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => 'Специализации',
                'attr'   =>  array(
                    'class'   => 'js-example-basic-multiple',
                    'style' => 'width: 100%')
            ));    
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
