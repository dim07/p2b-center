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
use Doctrine\ORM\EntityRepository;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class reportPaysFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $admin = $options['admin'];
        $Legentity = $user->getLegentity();
//        $user_id = $user->getId();
        $leg_id = 0;
//        $data = $builder->getData();
        if (!is_null($Legentity)) {
            $leg_id =  $Legentity->getId();
        }
        $builder
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
            ->add('user', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\User',
                    'query_builder' => function (EntityRepository $er) use ($leg_id, $admin) {
                        if ($admin) {
                            return $er->createQueryBuilder('u')
                            ->select('DISTINCT u')    
                            ->innerJoin('u.orders','o');
                        } else {    
                            return $er->createQueryBuilder('u')
                            ->select('DISTINCT u')    
                            ->innerJoin('u.orders','o')
                            ->innerJoin('o.stage','s')  
                            ->innerJoin('s.project','j')    
                            ->where('j.ContractorId = :id')    
                            ->setParameter('id', $leg_id);
                        }
                    },
                    'choice_label' => 'fio',
                    'label' => 'Исполнитель',
                    'multiple' => FALSE,
                    'attr'   =>  array(
                        'class'   => 'js-example-basic-single',
                        'style' => 'width: 100%'),
                    'required' => true
            )) 
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
        $resolver->setRequired(array('user','admin'));
    }
}

