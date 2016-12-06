<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
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
        $builder->add('name', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('email', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('address', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('city', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('state', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('zip', NumberType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('phone', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('purchaseDate', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('retailerName', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('mattressModel', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('mattressSize', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('purchasePrice', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))

            ->add('contactedRetailer', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('shippingDamage', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('within48', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('lengthDifferent', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('bodyImpression', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('feelingIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('pillowIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('diffIssue', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control'), 'data' => 0))
            ->add('channel', HiddenType::class, array('required' => true, 'attr' => array('class' => 'form-control')))

            ->add('receiptCopy', FileType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('receiptPath', TextType::class, array('attr' => array('class' => 'button', 'style' => 'height:2.1em', 'readonly' => 'readonly', 'placeholder' => 'No file chosen'), 'required' => false))
            ->add('lawCopy', FileType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('lawPath', TextType::class, array('attr' => array('class' => 'button', 'style' => 'height:2.1em', 'readonly' => 'readonly', 'placeholder' => 'No file chosen'), 'required' => false))

            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\FrontWarrantyClaim'
        ));
    }

}