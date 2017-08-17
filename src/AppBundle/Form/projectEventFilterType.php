<?php

namespace AppBundle\Form;

/**
 * Description of reportEventFilterType
 *
 * @author dima
 */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
//use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class projectEventFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $proj_id = $options['proj_id'];
        $builder
            ->add('proj_id', HiddenType::class, array(
                'data' => $proj_id,)
            )
            ->add('EventType', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\EventType',
                'choice_label' => 'name',
                'label' => 'Тип события',
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'required' => false
            ))                
            ->add('dt1', DateTimePickerType::class, [
                'label' => 'От даты',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])    
             ->add('dt2', DateTimePickerType::class, [
                'label' => 'До даты',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])    
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
        $resolver->setRequired('proj_id');
    }
}
