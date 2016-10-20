<?php

namespace OrderBundle\Form;

use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewCreditType extends AbstractType
{
    private $tokenStorage;
    private $usersRepository;
    private $entityManager;

    /**
     * CreditRequestType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManager $entityManager
     */
    public function __construct( TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
            ->add('submittedForUser', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'label' => 'On Behalf of',
                    'placeholder' => 'Select Retailer or Distributor Requesting Credit',
                    //'choice_label' => 'fullname',


                    'choices' => $this->usersRepository->findDR($this->tokenStorage->getToken()->getUser()),

                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )

            ->add('amountRequested', MoneyType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onclick' => 'this.select()'),
                    'label' => 'Amount',
                    'currency' => 'USD',
                    'required' => true
                )
            )
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'maxlength' => '255'), 'required' => false));

        ;
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