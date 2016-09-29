<?php

namespace InventoryBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use AppBundle\Entity\User;
use OrderBundle\Entity\Orders;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class WarrantyClaimType extends AbstractType
{

    private $tokenStorage;

    /**
     * WarrantyClaimType constructor.
     * @param $tokenStorage
     */
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
            ->add('submittedForUser', EntityType::class, array(
                    'class' => 'AppBundle\Entity\User',
                    'label' => 'On Behalf of',
                    'placeholder' => 'Select User',
                    'choice_label' => function (User $user) {
                        return $user->getFullName();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )
            ->add('order', EntityType::class, array(
                    'class' => 'OrderBundle\Entity\Orders',
                    'label' => 'Order Number',
                    'placeholder' => 'Select Order Number',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')
                            ->where('o.submitted_for_user = :user')
                            ->orderBy('o.submitDate', 'DESC')
                            ->setParameter('user', $this->tokenStorage->getToken()->getUser());
                    },
                    'choice_label' => function (Orders $order) {
                        return $order->getOrderNumber();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )
            ->add('creditRequested', MoneyType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onclick' => 'this.select()'),
                    'label' => 'Amount',
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
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\WarrantyClaim'
        ));
    }
}
