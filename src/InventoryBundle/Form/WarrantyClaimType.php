<?php

namespace InventoryBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use InventoryBundle\Entity\ProductVariant;
use OrderBundle\Repository\OrdersRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
use AppBundle\Repository\UserRepository;


class WarrantyClaimType extends AbstractType
{

    private $tokenStorage;
    private $ordersRepository;
    private $usersRepository;

    /**
     * WarrantyClaimType constructor.
     * @param $tokenStorage
     * @param $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->ordersRepository = $entityManager->getRepository('OrderBundle:Orders');
        $this->usersRepository = $entityManager->getRepository('AppBundle:User');
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
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onchange' => 'getProductVariants()'),
                    'required' => false
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
                    'required' => false
                )
            )
//            ->add('creditRequested', MoneyType::class, array(
//                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'onclick' => 'this.select()'),
//                    'label' => 'Amount',
//                    'constraints' => array(
//                        new GreaterThan(array(
//                                'value' => 0,
//                                'message' => 'You must enter an amount greater than zero.')
//                        )
//                    ),
//                    'currency' => 'USD',
//                    'required' => true
//                )
//            )
            ->add('description', TextareaType::class, array('label' => 'Comments', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px')))
            ->add('file1', FileType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Proof Image 1',
                    'required' => true,
                )
            )
            ->add('path1', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;', 'onclick' => 'openFileBrowser1()', 'readonly' => 'readonly'),
                    'required' => false,
                )
            )
            ->add('file2', FileType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Proof Image 2',
                    'required' => false,
                )
            )
            ->add('path2', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;', 'onclick' => 'openFileBrowser2()', 'readonly' => 'readonly'),
                    'required' => false,
                )
            )
            ->add('file3', FileType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'label' => 'Proof Image 3',
                    'required' => false,
                )
            )
            ->add('path3', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;', 'onclick' => 'openFileBrowser3()', 'readonly' => 'readonly'),
                    'required' => false,
                )
            )
            ->add('lawLabel', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;'),
                    'required' => true,
                )
            )
            ->add('frLabel', TextType::class, array(
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px; margin-left: 10px;'),
                    'required' => true,
                )
            );
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
