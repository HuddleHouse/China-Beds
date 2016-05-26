<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('last_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('password', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
//            ->add('role', EntityType::class, array(
//                'class' => 'AppBundle:Office',
//                'label' => 'Office',
//                'choice_label' => 'name',
//                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
//                'required' => false
//            ))
            ->add('enabled', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Account Enabled',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
