<?php

namespace InventoryBundle\Form;

use AppBundle\Services\SettingsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OrderBundle\Entity\OrdersPopItem;
use OrderBundle\Entity\OrdersProductVariant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PromoKitOrderType extends AbstractType
{
    private $settingsService;
    private $repository;

    /**
     * PromoKitOrderType constructor.
     * @param SettingsService $settingsService
     * @param EntityManager $em
     */
    public function __construct(SettingsService $settingsService, EntityManager $em)
    {
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
                    'label' => 'Items',
                    'choice_label' => 'name',
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
                )
            )
            ->add('productVariants', EntityType::class, array(
                    'class' => 'OrderBundle\Entity\OrdersProductVariant',
                    'label' => 'Items',
                    'choice_label' => function (OrdersProductVariant $pv) {
                        return $pv->getProductVariant()->getProduct()->getName() . ' ' . $pv->getProductVariant()->getName();
                    },
                    'choices' => $this->repository->getProductVariants(),
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('pv')
//                            ->leftJoin('pv.warehouse_info', 'whi', 'WITH', 'pv.warehouse_info = whi')
//                            ->leftJoin('whi.warehouse', 'w', 'WITH', 'whi.warehouse = w');
//                    },
                )
            )
            ->add('popItems', EntityType::class, array(
                    'class' => 'OrderBundle\Entity\OrdersPopItem',
                    'label' => 'Items',
                    'choice_label' => function (OrdersPopItem $p) {
                        return $p->getPopItem()->getName();
                    },
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                    'required' => false,
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('p')
//                            ->andWhere('p.');
//                    },
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
