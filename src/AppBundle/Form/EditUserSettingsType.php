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

class EditUserSettingsType extends AbstractType
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
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'phone-input' => '', 'ng-model' => 'phone_val')))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('state', EntityType::class, array(
                'class' => 'AppBundle:State',
                'label' => 'State',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
            ))

            ->add('ach_routing_number', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('ach_account_number', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('distributor_fedex_number', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'label' => 'FedEx Number'), 'required' => false))

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
