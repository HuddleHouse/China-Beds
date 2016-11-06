<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrontWarrantyClaimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('email', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('address', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('city', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('state', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('zip', NumberType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('phone', NumberType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('purchaseDate', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('retailerName', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('mattressModel', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('mattressSize', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('purchasePrice', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))

            ->add('contactedRetailer', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('shippingDamage', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('within48', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('lengthDifferent', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('bodyImpression', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('feelingIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('pillowIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))
            ->add('diffIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control', 'id' => 'username', 'type' => 'text' )))


            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\FrontWarrantyClaim'
        ));
    }

}