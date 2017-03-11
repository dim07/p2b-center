<?php
// src/AppBundle/Form/ProfileFormType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
//use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('username');
        $builder->remove('email');
        $builder->remove('current_password');
        
        $builder
            ->add('fio', null, array('label' => 'ФИО',))
            ->add('hideName', null, array('label' => 'Скрывать ФИО',))  
            ->add('phone', null, array('label' => 'Телефон',))     
            ->add('region', null, array('label' => 'Регион',)) 
            ->add('sections', EntityType::class, array(
                'class' => 'AppBundle\Entity\Section',
                'choice_label' => 'name',
                'group_by' => 'spec',
//                'expanded' => true,
                'multiple' => true,
                'label' => 'Специализации',
                'attr'   =>  array(
                    'class'   => 'js-example-basic-multiple',
                    'style' => 'width: 100%')
            
            ))    
//            ->add('agreement', null, array('label' => 'Соглашение',))     
            ->add('imageFile', VichImageType::class, [
            'required' => false,
            'label' => 'Изображение',    
            'allow_delete' => true, // not mandatory, default is true
            'download_link' => true, // not mandatory, default is true
            ]);
//            ->add('imageFile', FileType::class, [
//            'required' => false,
//            'label' => 'Изображение',
//            'attr'   =>  array(
//                    'class'   => 'myfile')    
//            ]);
//            ->add('imageFile', 'genemu_jqueryimage');
        
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }
    

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}