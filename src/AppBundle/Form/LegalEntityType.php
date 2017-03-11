<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegalEntityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Наименование',))
            ->add('hideName', null, array('label' => 'Скрывать Наименование',))
            ->add('legalAdr', null, array('label' => 'Юридический адрес',))
            ->add('customer', null, array('label' => 'Заказчик',))
            ->add('contractor', null, array('label' => 'Подрядчик',));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LegalEntity'
        ));
    }
}
