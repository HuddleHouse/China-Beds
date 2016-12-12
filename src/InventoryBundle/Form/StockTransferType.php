<?php

namespace InventoryBundle\Form;

use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StockTransferType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array('attr' => array('class' => 'date-select', 'style' => 'margin-bottom: 10px')))
            ->add('message', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('departing_warehouse', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Warehouse',
                'label' => 'Departing Warehouse',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => true
            ))
            ->add('receiving_warehouse', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Warehouse',
                'label' => 'Receiving Warehouse',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => true
            ))
            ->add('status', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Status',
                'label' => 'Status',
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
            'data_class' => 'InventoryBundle\Entity\StockTransfer'
        ));
    }
}
