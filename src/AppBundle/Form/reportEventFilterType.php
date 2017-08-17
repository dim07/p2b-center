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
use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class reportEventFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $adm = $options['adm'];
        $Legentity = $user->getLegentity();
        $gip_id = $user->getId();
        $leg_id = 0;
        $data = $builder->getData();
        if (!is_null($Legentity)) {
            $leg_id =  $Legentity->getId();
        }
        $builder
            ->add('leg', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'query_builder' => function (EntityRepository $er) use ($gip_id, $adm) {
                    $qb = $er->createQueryBuilder('l');
                    $qb ->select('DISTINCT l')    
                        ->innerJoin('l.contractprojects','j')
                        ->innerJoin('j.gip','u');
                    if (!$adm) {
                        $qb->where('u.id = :id')    
                        ->setParameter('id', $gip_id);
                    }    
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
            ->add('project', Filters\EntityFilterType::class, array(
                'class' => 'AppBundle\Entity\project',
                'query_builder' => function (EntityRepository $er) use ($gip_id, $leg_id,$adm) {
                    $qb = $er->createQueryBuilder('j');
                    if (!$adm) {
                        if ($leg_id > 0) {
                            $qb->innerJoin('j.Contractor','l')
                               ->where('l.id = :id')    
                               ->setParameter('id', $leg_id); 
                        } else {
                            $qb->innerJoin('j.gip','u')
                               ->where('u.id = :id')    
                               ->setParameter('id', $gip_id);     
                        } 
                    }    
                    return $qb;
                },
                'choice_label' => 'name',
                'group_by' => ($leg_id>0 ? 'gip' : 'Contractor'),        
                'label' => 'Проект',
                'multiple' => FALSE,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'required' => false
            )) 
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
        $resolver->setRequired('user');
        $resolver->setRequired('adm');
    }
}
