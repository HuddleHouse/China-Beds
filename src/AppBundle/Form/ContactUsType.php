<?php

namespace AppBundle\Form;

use Leafo\ScssPhp\Node\Number;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ContactUsType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContactUs'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class , array('label'=>'First Name', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('lastName', TextType::class , array('label'=>'Last Name', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('email', EmailType::class, array('label'=>'Email', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('address', TextType::class , array('label'=>'Address', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('city', TextType::class , array('label'=>'City', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('state', TextType::class , array('label'=>'State', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('zip', NumberType::class, array('label'=>'Zip Code', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('phone', NumberType::class, array('label'=>'Phone', 'required'=>true, 'attr' => array('class' => 'form-control')))
            ->add('message', TextType::class, array('label'=>'Message', 'required'=>true, 'attr' => array('class' => 'form-control', 'id' => 'message')))
            ->getForm();
    }

}