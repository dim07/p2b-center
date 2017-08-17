<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class OrderPayType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('planPay', MoneyType::class, array(
                'label' => 'Величина заявленная', 
                'currency' => 'RUB',
                'required' => false
            ))
            ->add('factPay', MoneyType::class, array(
                'label' => 'Величина утвержденная', 
                'currency' => 'RUB',
                'required' => false
            ))
            ->add('payDate', DateTimePickerType::class, [
                'label' => 'Дата выплаты',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
            ->add('chargDate', DateTimePickerType::class, [
                'label' => 'Дата начисления',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
            ->add('statementDate', DateTimePickerType::class, [
                'label' => 'Дата заявления на выплату',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrderPay'
        ));
    }
}
