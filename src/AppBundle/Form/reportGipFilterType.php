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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class reportGipFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $adm = $options['adm'];
        $Legentity = $user->getLegentity();
        $leg_id = 0;
//        $data = $builder->getData();
        if (!is_null($Legentity)) {
            $leg_id =  $Legentity->getId();
        }
        $builder
            ->add('leg', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('l');
                    $qb ->select('DISTINCT l')    
                        ->innerJoin('l.contractprojects','j');
                    return $qb;
                },
                'choice_label' => 'name',
                'label' => 'Генподрядчик',
                'multiple' => FALSE,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'required' => false
            ))
            ->add('gip', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($leg_id, $adm) {
                    $qb = $er->createQueryBuilder('l');
                    $qb ->select('DISTINCT l')    
                        ->innerJoin('l.gipprojects','j')
                        ->innerJoin('j.Contractor','u');
                    if (!$adm) {
                        $qb->where('u.id = :id')    
                        ->setParameter('id', $leg_id);
                    }    
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
//            ->add('status',  ChoiceType::class, array(
//                'label' => 'Статус отчета',
////                'apply_filter' => false, // disable filter
//                'attr'   =>  array(
//                    'class'   => 'js-example-basic-single',
//                    'style' => 'width: 100%'),
//                'required' => false,
//                'choices'  => array(
//                    'Смотреть по всем генподрядчикам' => 0,
//                    'Смотреть по выбранному генподрядчику' => 1,
//                ),
//                'data' => 0,
//            )) 
            ->add('status', CheckboxType::class, array(
                'label'     => 'Смотреть по выбранному генподрядчику',
                'required'  => false,
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
        $resolver->setRequired('adm');
    }
}
