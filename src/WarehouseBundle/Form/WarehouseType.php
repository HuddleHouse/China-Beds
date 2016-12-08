<?php

namespace WarehouseBundle\Form;

use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WarehouseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('email_2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('address1', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('address2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => true))
            ->add('zip', NumberType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => true))
            ->add('manager_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('management_comp', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('shipping_label_image_type', ChoiceType::class, array('choices' => array('PNG' => 'PNG', 'PDF' => 'PDF', 'DPL' => 'DPL', 'EPL2' => 'EPL2', 'ZPLII' => 'ZPLII'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => true))
            ->add('state', EntityType::class, array(
                'class' => 'AppBundle:State',
                'label' => 'State',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
            ))
            ->add('channel',  EntityType::class, array(
                'class' => 'InventoryBundle:Channel',
                'label' => 'Channel',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control'),
                'expanded'  => true,
                'multiple' => false,
                'required' => false
            ))
            ->add('active', ChoiceType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                ),
                'choices' => array(
                    'Yes' => true,
                    'No' => false
                ),
                'empty_data' => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WarehouseBundle\Entity\Warehouse'
        ));
    }
}
