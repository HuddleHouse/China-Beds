<?php

namespace OrderBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class CreditRequestType extends AbstractType
{
    private $tokenStorage;
    private $ordersRepository;

    /**
     * CreditRequestType constructor.
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
        $builder
//            ->add('submittedForUser', EntityType::class, array(
//                    'class' => 'AppBundle\Entity\User',
//                    'label' => 'On Behalf of',
//                    'placeholder' => 'Select User Requesting Credit',
//                    'choice_label' => function (User $user) {
//                        return $user->getFullName();
//                    },
//                    'choices' => $this->usersRepository->findByUser($this->tokenStorage->getToken()->getUser()),
//                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
//                    'required' => true
//                )
//            )

            ->add('submittedForUser', TextType::class, array(
//                    'class' => 'AppBundle\Entity\User',
                    'label' => 'On Behalf of',
                    'data' => $this->tokenStorage->getToken()->getUser(),
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true,
                    'disabled' => true
                )
            )

            ->add('amountRequested', MoneyType::class, array(
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
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'maxlength' => '255'), 'required' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OrderBundle\Entity\Ledger'
        ));
    }
}
