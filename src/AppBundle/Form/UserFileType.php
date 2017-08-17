<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Jb\Bundle\FileUploaderBundle\Form\Type\FileAjaxType;
use Jb\Bundle\FileUploaderBundle\Form\Type\ImageAjaxType;

class UserFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Наименование',
//                'required' => false
            ))  
            ->add('File', ImageAjaxType::class, array(
                'img_width' => 400,
                'img_height' => 550,
                'endpoint' => 'gallery',
                'download_link' => TRUE,
                'remove_link' => FALSE,
                'label' => 'Cкан или фото (формат: jpg,png,gif; макс: 1600х1600, 1M)',
//                'progress' => TRUE,
                'required' => true
            ))    

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UserFile'
        ));
    }
}
