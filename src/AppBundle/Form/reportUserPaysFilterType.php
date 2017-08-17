<?php

namespace AppBundle\Form;

/**
 * Description of reportPaysFilterType
 *
 * @author dima
 */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Doctrine\ORM\EntityRepository;
//
//use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class reportUserPaysFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
    }
}

