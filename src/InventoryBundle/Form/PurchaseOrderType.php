<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PurchaseOrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderDate', DateType::class, array('attr' => array('class' => 'order-date', 'style' => 'margin-bottom: 10px')))
            ->add('stockDueDate', DateType::class, array('attr' => array('class' => 'due-date', 'style' => 'margin-bottom: 10px')))
            ->add('orderNumber', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('message', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('status', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Status',
                'label' => 'Status',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => true
            ))
            ->add('warehouse', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Warehouse',
                'label' => 'Warehouse',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\PurchaseOrder'
        ));
    }
}
