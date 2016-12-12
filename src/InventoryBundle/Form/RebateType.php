<?php

namespace InventoryBundle\Form;

use InventoryBundle\Entity\Channel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RebateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => true))
            ->add('channel', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\Channel',
                    'label' => 'Channel',
                    'placeholder' => 'Select Channel',
                    'choice_label' => function (Channel $channel) {
                        return $channel->getName();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onchange' => 'getProductVariants()'),
                    'required' => true
                )
            )
            ->add('description', TextAreaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => true));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Rebate'
        ));
    }
}
