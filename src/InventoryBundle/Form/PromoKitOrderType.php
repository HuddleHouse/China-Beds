<?php

namespace InventoryBundle\Form;

use AppBundle\Services\SettingsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use InventoryBundle\Entity\PopItem;
use InventoryBundle\Entity\ProductVariant;
use InventoryBundle\Entity\PromoKit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PromoKitOrderType extends AbstractType
{
    private $tokenStorageInterface;
    private $settingsService;
    private $repository;

    /**
     * PromoKitOrderType constructor.
     * @param TokenStorageInterface $tokenStorageInterface
     * @param SettingsService $settingsService
     * @param EntityManager $em
     */
    public function __construct(TokenStorageInterface $tokenStorageInterface, SettingsService $settingsService, EntityManager $em)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->settingsService = $settingsService;
        $this->repository = $em->getRepository('InventoryBundle:PromoKitOrders');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', EntityType::class, array(
                    'class' => 'AppBundle\Entity\State',
                    'label' => 'Ship State',
                    'choice_label' => 'name',
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => true,
                )
            )
            ->add('promoKitItems', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\PromoKit',
                    'label' => 'Promo Kit Items',
                    'choice_label' => function (PromoKit $p) {
                        return $p->getName() . ': ' . $p->getDescription();
                    },
                    'choices' => $this->repository->getActivePromoKitItems(),
                    'expanded' => false,
                    'multiple' => true,
                    'attr' => array('class' => 'form-group select2', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
                )
            )
            ->add('productVariants', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\ProductVariant',
                    'label' => 'Products',
                    'choice_label' => function (ProductVariant $pv) {
                        return $pv->getProduct()->getName() . ' ' . $pv->getName();
                    },
                    'choices' => $this->repository->getProductVariants($this->tokenStorageInterface->getToken()->getUser()->getActiveChannel(), $this->settingsService->get('default-warehouse')),
                    'expanded' => false,
                    'multiple' => true,
                    'attr' => array('class' => 'form-group select2', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
                )
            )
            ->add('popItems', EntityType::class, array(
                    'class' => 'InventoryBundle\Entity\PopItem',
                    'label' => 'Pop Items',
                    'choice_label' => function (PopItem $p) {
                        return $p->getName() . ': ' . $p->getDescription();
                    },
                    'expanded' => false,
                    'multiple' => true,
                    'attr' => array('class' => 'form-group select2', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
                )
            )
            ->add('retailerStoreName', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Retailer Store Name'))
            ->add('shipContact', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Ship Contact Name'))
            ->add('shipCity', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Ship City'))
            ->add('shipZip', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Ship Zip'))
            ->add('shipPhone', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Ship Phone'))
            ->add('shipAddress', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Ship Address'))
            ->add('shipAddress2', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false, 'label' => 'Ship Address 2'))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\PromoKitOrders'
        ));
    }
}
