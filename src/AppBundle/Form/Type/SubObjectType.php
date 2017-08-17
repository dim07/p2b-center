<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\SubObjectTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of SubObjectType
 *
 * @author dima
 */
class SubObjectType extends AbstractType 
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $transformer = new SubObjectTransformer($this->manager);
//        $builder->addModelTransformer($transformer);
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected subobject does not exist',
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
