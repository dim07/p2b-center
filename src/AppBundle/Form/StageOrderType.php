<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;
//use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class StageOrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $object = $builder->getData();
        $proj_id = $object->getStage()->getIdProject();
        $order_id = $object->getId();
        $user = $options['user'];
        $project = $object->getStage()->getProject(); 
        
        $builder
            ->add('name', null, array('label' => 'Наименование', 'required' => true,'disabled' => ($user and $user->getId()===$project->getGipId()),))    
            ->add('section', EntityType::class, array(
                'class' => 'AppBundle\Entity\Section',
                'choice_label' => 'name',
                'group_by' => 'spec',
//                'expanded' => true,
                'multiple' => false,
                'label' => 'Раздел специализации',
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'empty_data' => null,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
                'required' => false,
 
            ))    
            ->add('cost', MoneyType::class, array('label' => 'Стоимость, план', 'currency' => 'RUB', 'required' => false,'disabled' => ($user and $user->getId()===$project->getGipId()),))
            ->add('startDate', DateTimePickerType::class, [
                'label' => 'Дата начала заказа',
                'format' => 'dd.MM.yyyy',
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            ])
            ->add('factEndDate', DateTimePickerType::class, [
                'label' => 'Дата завершения заказа',
                'format' => 'dd.MM.yyyy',   
                'disabled' => ($user and $user->getId()===$project->getGipId()),
                'required' => false
            ])    
                
            ->add('AfterOrder', EntityType::class, array(
                'class' => 'AppBundle\Entity\StageOrder',
                'query_builder' => function (EntityRepository $er) use ($proj_id) {
                    return $er->createQueryBuilder('o')
                        ->innerJoin('o.stage','s')
                        ->innerJoin('s.project','p')    
                        ->where('p.id = :id')    
                        ->orderBy('o.endDate', 'ASC')
                        ->setParameter('id', $proj_id);
                },
                'group_by' => 'stage',        
                'label'    => 'Зависит от заказа',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'required' => false,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            )) 
            ->add('isLegalEntity', CheckboxType::class, array(
                'label'    => 'Субподрядчик (ю.л.)',
                'required' => false,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            ))    
            ->add('Contractor', EntityType::class, array(
                'class' => 'AppBundle\Entity\LegalEntity',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.contractor = 1')    
                        ->orderBy('l.name', 'ASC');
                },
                'label'    => 'Субподрядчик (ю.л.)',
                'choice_label' => 'name',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'required' => false,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            )) 
            ->add('UserIsp', EntityType::class, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.legentity','l')
                        ->where('l.id is NULL OR ((l.customer IS NULL OR l.customer = 0) AND (l.contractor IS NULL OR l.contractor = 0))')    
                        ->orderBy('u.rating, u.fio', 'ASC');
                },
                'label'    => 'Исполнитель (ф.л.)',
                'choice_label' => 'fio',
                'placeholder' => 'Сделайте выбор',
                'empty_data' => null,
                'attr'   =>  array(
                    'class'   => 'js-example-basic-single',
                    'style' => 'width: 100%'),
                'disabled' => ($user and $user->getId()===$project->getGipId()),
                'required' => false
 
            ))
                
            ->add('isPublic', CheckboxType::class, array(
                'label'    => 'Опубликовано',
                'required' => false,
                'disabled' => ($user and $user->getId()===$project->getGipId()),
            ))    
//            ->add('endDate', DateTimePickerType::class, [
//                'label' => 'Дата завершения заказа план',
//                'format' => 'dd.MM.yyyy',
//                
//            ])
//            
//            ->add('factCost')
            
            ->add('info', TextareaType::class, array(
                'label' => 'Примечание', 
                'attr' => array('rows' => '4'),
                'disabled' => ($user and $user->getId()===$project->getGipId()),
                'required' => false
                ))
                        
//            ->add('offer', EntityType::class, array(
//                'class' => 'AppBundle\Entity\Offer',
//                'query_builder' => function (EntityRepository $er) use ($order_id) {
//                
//                    return $er->createQueryBuilder('o')
//                        ->where('o.order_id = '.$order_id)    
//                        ->orderBy('o.cost', 'ASC');
//                },
//                'label'    => 'Выбор предложения',
////                'choice_label' => 'fio',
//                'placeholder' => 'Сделайте выбор',
//                'empty_data' => null,
//                'attr'   =>  array(
//                    'class'   => 'js-example-basic-single',
//                    'style' => 'width: 100%'),
//                'required' => false
// 
//            ))            
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StageOrder'
        ));
        $resolver->setRequired('user');
    }
}
