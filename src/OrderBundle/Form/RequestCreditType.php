<?php

namespace OrderBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\User;
use OrderBundle\Entity\Orders;
use InventoryBundle\Entity\ProductVariant;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\CreditRequest;
use Symfony\Component\Form\AbstractType;

class RequestCreditType extends AbstractType
{

    private $tokenStorage;
    private $usersRepository;

    /**
     * RequestCreditType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManager $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->usersRepository = $entityManager->getRepository('AppBundle:User');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken();

        $builder
            ->add('submittedForUser', EntityType::class, array(
                    'class' => 'AppBundle\Entity\User',
                    'label' => 'User',
                    'placeholder' => 'Select User',
                    'choices' => $this->usersRepository->findUsersForUser($this->tokenStorage->getToken()->getUser()),
                    'choice_label' => function (User $user) {
                        return $user->getDisplayName();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onchange' => 'getOrders()'),
                    'required' => true,
                )
            )
            ->add('order', EntityType::class, array(
                    'class' => 'OrderBundle\Entity\Orders',
                    'label' => 'Order ID',
                    'placeholder' => 'Select Order ID',
                    'choices' => [],
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onchange' => 'getProductVariants()'),
                    'required' => true
                )
            )
            ->add('productVariant', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\ProductVariant',
                    'label' => 'Product',
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'disabled' => 'disabled'),
                    'placeholder' => 'Select Order ID first',
                    'choice_label' => function(ProductVariant $productVariant) {
                        return $productVariant->getProduct()->getName() . ' ' . $productVariant->getName();
                    },
                    'required' => true
                )
            )
            ->add('requestAmount', MoneyType::class, array(
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onclick' => 'this.select()', 'placeholder' => '0.00'
                ),
                    'label' => 'Amount',
                    'currency' => 'USD',
                    'required' => true
                )
            )

            ->add('comments', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 0px', 'maxlength' => '255'), 'required' => true))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OrderBundle\Entity\CreditRequest'
        ));
    }





}