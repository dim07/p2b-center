<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class projectType extends AbstractType
{
//    private $tokenStorage;
//
//    public function __construct(TokenStorageInterface $tokenStorage)
//    {
//        $this->tokenStorage = $tokenStorage;
//    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         // grab the user, do a quick sanity check that one exists
//        $user = $this->tokenStorage->getToken()->getUser();
//        if (!$user) {
//            throw new \LogicException(
//                'This cannot be used without an authenticated user!'
//            );
//        }
        $user = $options['user'];
        $project = $builder->getData();
        $builder
            ->add('name', null, array(
                'label' => 'Наименование',
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            ))
            ->add('nofot', NumberType::Class, array(
                'label' => 'Норма отчисления ФОТ', 
                'scale' => 3,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            ))
//            ->add('info', null, array('label' => 'Информация',))
            ->add('Customer', EntityType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single'),
                'label' => 'Заказчик',
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            )) 
            ->add('Contractor', EntityType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single'),
                'label' => 'Подрядчик',
                'disabled' => ($user and $user->getId()===$project->getGipId()),
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
                'disabled' => ($user and $user->getId()===$project->getGipId()),
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
        $resolver->setRequired('user');
    }
}
