<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Jb\Bundle\FileUploaderBundle\Form\Type\FileAjaxType;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
//use Jb\Bundle\FileUploaderBundle\Form\Type\ImageAjaxType;

class StageOrderEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eventDate', DateTimePickerType::class, [
                'label' => 'Дата события',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
            ->add('info', TextareaType::class, array(
                'label' => 'Примечание', 
                'attr' => array('rows' => '4'),
                'required' => false
                ))
//            ->add('File', ImageAjaxType::class, array(
//                'img_width' => 400,
//                'endpoint' => 'gallery'
//            ))
            ->add('EventType', EntityType::class, array(
                'class' => 'AppBundle\Entity\EventType',
                'choice_label' => 'name',
                'placeholder' => 'Не выбрано',
                'empty_data' => null,
                'label' => 'Тип события',
//                'required' => false
 
            ))  
            ->add('File', FileAjaxType::class, array(
//                'img_width' => 400,
                'endpoint' => 'doc_store',
//                'endpoint' => 'gallery',
                'download_link' => TRUE,
//                'remove_link' => TRUE,
                'label' => 'Файл',
//                'progress' => TRUE,
                'required' => false
            ))    

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StageOrderEvent'
        ));
    }
}
