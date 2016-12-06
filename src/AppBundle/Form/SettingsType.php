<?php

namespace AppBundle\Form;

use AppBundle\Entity\SettingSection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('label', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('value', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('section', EntityType::class, array(
                    'class' => 'AppBundle\Entity\SettingSection',
                    'label' => 'Section',
                    'choice_label' => function (SettingSection $section) {
                        return $section->getName();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )
            ->add('type', ChoiceType::class, array(
                    'label' => 'Type',
                    'choices' => array('text' => 'text' , 'select' => 'select', 'textarea' => 'textarea'),
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )
            ->add('choices', TextareaType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'placeholder' => 'choice 1,choice 2,choice 3,...'),
                    'required' => false,
                    'label' => 'Choices for a setting of type select, comma-separated (use \'yes,no\' for boolean choices)',
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Settings'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_settings';
    }


}
