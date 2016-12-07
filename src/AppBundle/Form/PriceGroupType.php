<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PriceGroupType extends AbstractType
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
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('users',  EntityType::class, array(
                'class' => 'AppBundle:User',
                'label' => 'Users',
                'choice_label' => function (User $user) {
                    return $user->getDisplayName();
                },
                'query_builder' => function (UserRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.user_channels', 'c')
                        ->where('c = :channel')
                        ->setParameter('channel', $this->tokenStorage->getToken()->getUser()->getActiveChannel());
                },
                'attr' => array('class' => 'form-control select2', 'style' => 'padding: 5px;margin-bottom: 50px;'),
                'expanded'  => false,
                'multiple' => true))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PriceGroup'
        ));
    }
}
