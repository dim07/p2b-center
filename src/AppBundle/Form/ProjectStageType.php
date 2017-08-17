<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectStageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num', IntegerType::class, array('label' => '№',))    
            ->add('name', null, array('label' => 'Наименование',))
            ->add('cost', MoneyType::class, array(
                'label' => 'Стоимость', 
                'currency' => 'RUB',
                'required' => false
            )) 
            ->add('endDate', DateTimePickerType::class, [
                'label' => 'Дата завершения, план',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])    
            ->add('FactEndDate', DateTimePickerType::class, [
                'label' => 'Дата завершения, факт',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])    
//            ->add('endDate', DateType::class, array(
//                    'widget' => 'single_text',
//                    // do not render as type="date", to avoid HTML5 date pickers
//                    'html5' => false,
//                    'format' => 'dd.mm.yyyy',
//                    
//                    // add a class that can be selected in JavaScript
//                    'attr' => ['class' => 'js-datepicker'],
//                    )
//            )
            ->add('info', TextareaType::class, array(
                'label' => 'Описание', 
                'attr' => array('rows' => '4'),
                'required' => false
                ))
//            ->add('project', EntityType::class, array(
//                'class' => 'AppBundle\Entity\project',
//                'choice_label' => 'name',
//                'placeholder' => 'Please choose',
//                'empty_data' => null,
//                'required' => false
// 
//            )) 
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProjectStage',
            'csrf_protection' => false,
        ));
    }
}
