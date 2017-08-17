<?php

namespace AppBundle\Form;

/**
 * Description of reportPaysFilterType
 *
 * @author dima
 */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class reportLegPaysFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $approved = $options['approved'];
        $builder
            ->add('approved', HiddenType::class, array(
                'data' => $approved,)
            )    
            ->add('leg', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\LegalEntity',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->select('DISTINCT l')    
                            ->innerJoin('l.contractprojects','j');
                    },
                    'choice_label' => 'name',
                    'label' => 'Генподрядчик',
                    'multiple' => FALSE,
                    'attr'   =>  array(
                        'class'   => 'js-example-basic-single',
                        'style' => 'width: 100%'),
                    'required' => false
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
        $resolver->setRequired(array('approved'));
    }
}

