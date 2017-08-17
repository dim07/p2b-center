<?php

namespace AppBundle\Form;

/**
 * Description of reportEventFilterType
 *
 * @author dima
 */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use AppBundle\Form\Type\DateTimePickerType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class confirmPaysFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $Legentity = $user->getLegentity();
        $leg_id = 0;
        if (!is_null($Legentity)) {
            $leg_id =  $Legentity->getId();
        }
        $builder
            ->add('gip', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($leg_id) {
                    $qb = $er->createQueryBuilder('l');
                    $qb ->select('DISTINCT l')    
                        ->innerJoin('l.gipprojects','j')
                        ->innerJoin('j.Contractor','u')
                        ->where('u.id = :id')    
                        ->setParameter('id', $leg_id);
                    return $qb;
                },
                'choice_label' => 'fio',
                'label' => 'ГИП',
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
        $resolver->setRequired('user');
    }
}
