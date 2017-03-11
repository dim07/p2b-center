<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class projectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Наименование',))
            ->add('nofot', NumberType::Class, 
                    array('label' => 'Норма отчисления ФОТ', 'scale' => 3,))
//            ->add('info', null, array('label' => 'Информация',))
            ->add('Customer', EntityType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single'),
                'label' => 'Заказчик'
 
            )) 
            ->add('Contractor', EntityType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single'),
                'label' => 'Подрядчик'
 
            )) 
            ->add('gip', EntityType::class, array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'fio',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single'),
                'label' => 'ГИП',
                'required' => false,
 
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\project',
            'csrf_protection' => false,
        ));
    }
}
