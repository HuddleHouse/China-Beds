<?php

namespace InventoryBundle\Form;

use Doctrine\ORM\EntityManager;
use InventoryBundle\Entity\Channel;
use OrderBundle\Entity\Orders;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use OrderBundle\Repository\OrdersRepository;


class RebateSubmissionType extends AbstractType
{
    private $tokenStorage;
    private $usersRepository;

    /**
     * RebateSubmissionType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManager $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->ordersRepository = $entityManager->getRepository('OrderBundle:Orders');
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order', EntityType::class, array(
                    'class' => 'OrderBundle\Entity\Orders',
                    'label' => 'Order ID',
                    'placeholder' => 'Select Order ID',
                    'choices' => $this->ordersRepository->getLatestOrdersForUser($this->tokenStorage->getToken()->getUser()),
                    'choice_label' => function (Orders $order) {
                        return $order->getOrderId();
                    },
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true
                )
            )
            ->add('rebate', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\Rebate',
                    'label' => 'Rebate',
                    'choice_label' => 'name',
                    'choices' => $builder->getData()->getChannel()->getRebates(),
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true,
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
            ->add('file', FileType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Copy of Invoice',
                    'required' => true,
                )
            )
            ->add('path', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;', 'onclick' => 'openFileBrowser()', 'readonly' => 'readonly'),
                    'required' => true,
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
