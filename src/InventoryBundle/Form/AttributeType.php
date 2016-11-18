<?php

namespace InventoryBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use InventoryBundle\Entity\Channel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AttributeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('alt_tag', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false,))
            ->add('file', FileType::class, array(
                'attr' => array('style' => 'margin-bottom: 29px'),
                'label' => 'Picture',
                'required' => false,
            ))
            ->add('channels', EntityType::class, array('attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'required' => false,
                'class' => 'InventoryBundle\Entity\Channel',
                'multiple' => false,
                'choice_label' => 'name',
                'label' => 'Channel Assign'
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Attribute'
        ));
    }
}
