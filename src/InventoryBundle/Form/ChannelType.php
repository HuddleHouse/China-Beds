<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ChannelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Site Name'))
            ->add('url', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Site URL'))
            ->add('frontLogo',  FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Front Page Logo', 'label_attr' => array('class' => 'file-input')))
            ->add('fbLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Facebook Link'))
            ->add('twLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Twitter Link'))
            ->add('instaLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Instagram Link'))
            ->add('frontSliderOne', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'First Slider', 'label_attr' => array('class' => 'file-input')))
            ->add('frontSliderTwo', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Second Slider', 'label_attr' => array('class' => 'file-input')))
            ->add('frontSliderThree', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Third Slider', 'label_attr' => array('class' => 'file-input')))
            ->add('frontFooterOne', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'First Footer Box', 'label_attr' => array('class' => 'file-input')))
            ->add('frontFooterTwo', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Second Footer Box', 'label_attr' => array('class' => 'file-input')))
            ->add('frontFooterThree', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Third Footer Box', 'label_attr' => array('class' => 'file-input')))
            ->add('frontFooterText', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Footer Text'))

            ->add('')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Channel'
        ));
    }
}
