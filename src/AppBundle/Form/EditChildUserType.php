<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EditChildUserType extends AbstractType
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
            ->add('address_1', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('address_2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('company_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('zip', NumberType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('state', EntityType::class, array(
                'class' => 'AppBundle:State',
                'label' => 'State',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
            ))
            ->add('is_residential', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Residential Address?',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
        ;
    }

    public function onPreSetData($event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if(!$user->hasRole('ROLE_RETAILER') && !$user->hasRole('ROLE_DISTRIBUTOR')) {
            $form->add('is_show_credit', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Hide Credit Form/Status?',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
                ->add('is_show_warranty', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Hide Warranty Form/Status?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ))
                ->add('is_volume_discount', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Grant volume discount?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ))
                ->add('is_charge_shipping', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Charge Shipping?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ))
                ->add('sent_retail_kit', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Retailer Kit Sent?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ))
                ->add('is_current_retailer', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Current?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ))
                ->add('is_online_intentions', ChoiceType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Online Intentions?',
                    'choices' => array(
                        'Yes' => 1,
                        'No' => 0,
                    ),
                ));
        }
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
