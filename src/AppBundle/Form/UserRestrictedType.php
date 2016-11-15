<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserRestrictedType extends AbstractType
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
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'phone-input' => '', 'ng-model' => 'phone_val')))
            ->add('state', EntityType::class, array(
                'class' => 'AppBundle:State',
                'label' => 'State',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
            ))
            ->add('enabled', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Account Enabled',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
            ->add('is_residential', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Residential Address?',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
            ->add('is_show_credit', ChoiceType::class, array(
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
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                array($this, 'onPostSubmitData')
            )
        ;
    }

    public function onPostSubmitData($event) {
        $user = $event->getData();
        $form = $event->getForm();

    }

    public function onPreSetData($event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if($user->hasRole('ROLE_DISTRIBUTOR')) {
            $form->add('retailers', EntityType::class, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 5');
                },
                'label' => 'Retailers',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ));
        }
        if($user->hasRole('ROLE_WAREHOUSE')) {
            $form->add('managed_warehouses', EntityType::class, array(
                'class' => 'WarehouseBundle\Entity\Warehouse',
                'label' => 'Managed Warehouses',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 150px; '),
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ));
        }
        if ($user->hasRole('ROLE_SALES_REP')) {
            $form->add('distributors', EntityType::class, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 3');
                },
                'label' => 'Distributors',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; '),
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ));
        }
        if ($user->hasRole('ROLE_SALES_MANAGER')) {
            $form->add('sales_reps', null, array(
                'class' => 'AppBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 6');
                },
                'label' => 'Sales Reps',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => false,
                'multiple' => true,
                'expanded' => true
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