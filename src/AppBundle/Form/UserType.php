<?php

namespace AppBundle\Form;

use AppBundle\Entity\PriceGroup;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use WarehouseBundle\Entity\Warehouse;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px') ))
            ->add('plain_password', PasswordType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('first_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('last_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('address_1', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('address_2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('additional_emails', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('company_name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('zip', NumberType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'phone-input' => '')))
            ->add('state', EntityType::class, array(
                'class' => 'AppBundle:State',
                'label' => 'State',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
            ))
            ->add('groups', EntityType::class, array(
                'class' => 'AppBundle:Role',
                'label' => 'Roles',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px; width:300px;'),
                'multiple' => true,
                'required' => false
            ))
            ->add('price_groups', EntityType::class, array(
                'class' => 'AppBundle:PriceGroup',
                'label' => 'Price Groups',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('g')
                        ->where('g.channel in (:channel)')
                        ->setParameter('channel',  $options['data']->getUserChannels());
                },
                'choice_label' => function (PriceGroup $group) {
                    return sprintf('[%s] %s', $group->getChannel()->getName(), $group->getName());
                },
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'multiple' => true,
                'required' => false
            ))
            ->add('warehouses', EntityType::class, array(
                'class' => 'WarehouseBundle\Entity\Warehouse',
                'label' => 'Warehouses',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('w')
                        ->where('w.channel in (:channel)')
                        ->andWhere('w.active = true')
                        ->setParameter('channel',  $options['data']->getUserChannels());
                },
                'choice_label' => function (Warehouse $warehouse) {
                    return sprintf('[%s] %s', $warehouse->getChannel()->getName(), $warehouse->getName());
                },
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'multiple' => true,
                'required' => false
            ))
            ->add('user_channels', EntityType::class, array(
                'class' => 'InventoryBundle\Entity\Channel',
                'label' => 'Channels',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'multiple' => true,
                'required' => false
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
            ->add('my_distributor', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 3');
                },
                'label' => 'Distributor',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => false
            ))
            ->add('my_sales_rep', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 6');
                },
                'label' => 'Sales Rep',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => false
            ))
            ->add('my_sales_manager', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g')
                        ->where('g.id = 4');
                },
                'label' => 'Sales Manager',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'required' => false
            ))
            ->add('hide_rebate', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Hide Rebate Form from User',
                'choices' => array(
                    'No' => 0,
                    'Yes' => 1,
                )
            ))
            ->add('hideCC', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Hide Credit Card Payment Option',
                'choices' => array(
                    'No' => 0,
                    'Yes' => 1,
                )
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

//        if($user->hasRole('ROLE_DISTRIBUTOR')) {
//            $form->add('retailers', EntityType::class, array(
//                'class' => 'AppBundle\Entity\User',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->leftJoin('u.groups', 'g')
//                        ->where('g.id = 5');
//                },
//                'label' => 'Retailers',
//                'choice_label' => 'name',
//                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
//                'required' => false,
//                'multiple' => true,
//            ));
//        }
        if($user->hasRole('ROLE_WAREHOUSE')) {
            $form->add('managed_warehouses', EntityType::class, array(
                'class' => 'WarehouseBundle\Entity\Warehouse',
                'label' => 'Managed Warehouses',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 150px; '),
                'required' => false,
                'multiple' => true,
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
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px; '),
                'required' => false,
                'multiple' => true,
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
                'attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'required' => false,
                'multiple' => true,
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
