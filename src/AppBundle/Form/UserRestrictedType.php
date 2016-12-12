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
