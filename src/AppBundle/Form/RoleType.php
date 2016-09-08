<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RoleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
//            ->add('users',  EntityType::class, array(
//                'class' => 'AppBundle:User',
//                'label' => 'Users',
//                'choice_label' => 'name',
//                'attr' => array('class' => 'form-control', 'style' => 'padding: 5px;margin-bottom: 50px;'),
//                'expanded'  => true,
//                'multiple' => true))
            ->add('children',  EntityType::class, array(
                'class' => 'AppBundle:Role',
                'label' => 'Chidren Roles:',
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control', 'style' => 'padding: 5px;margin-bottom: 50px;'),
                'expanded'  => true,
                'multiple' => true))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Role'
        ));
    }
}
