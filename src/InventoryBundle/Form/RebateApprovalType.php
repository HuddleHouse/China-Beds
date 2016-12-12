<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;


class RebateApprovalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creditIssued', ChoiceType::class, array(
                    'label' => 'Issue Credit?',
                    'choices' => array('No' => false, 'Yes' => true),
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true,
                )
            )
            ->add('amountIssued', MoneyType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onclick' => 'this.select()', 'onkeydown' => 'this.value = moneyFormat(this.value)'),
                    'label' => 'Amount to Issue',
                    'constraints' => array(
                        new GreaterThan(array(
                                'value' => 0,
                                'message' => 'You must enter an amount greater than zero.')
                        )
                    ),
                    'currency' => 'USD',
                    'required' => true
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\RebateSubmission'
        ));
    }
}
