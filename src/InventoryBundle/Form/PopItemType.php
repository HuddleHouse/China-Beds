<?php

namespace InventoryBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use InventoryBundle\Entity\Channel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use WarehouseBundle\Repository\WarehouseRepository;

class PopItemType extends AbstractType
{

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->warehouseRepository = $entityManager->getRepository('WarehouseBundle:Warehouse');

    }

    private $channel;
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Channel channel */
        $this->channel = $builder->getData()->getChannel();
        $builder
            ->add('sku', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('pricePer', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('shippingPer', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'required' => false))
            ->add('warehouses', EntityType::class, array('attr' => array('class' => 'form-control select2', 'style' => 'margin-bottom: 10px'),
                'required' => false,
                'class' => 'WarehouseBundle\Entity\Warehouse',
                'multiple' => true,
                'choices' => $this->warehouseRepository->findByChannel([$this->tokenStorage->getToken()->getUser()->getActiveChannel()]),
                'choice_label' => 'name'
            ))
            ->add('active', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
            ->add('isHideOnOrder', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Hide on order form?',
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0,
                ),
            ))
            ->add('list_id', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px', 'readonly' => 'readonly'), 'required' => false))
            ->add('file', FileType::class, array(
                'attr' => array('style' => 'margin-bottom: 29px'),
                'label' => 'Picture',
                'required' => false,
            ))
            ->add('promo_kit_available', ChoiceType::class, array(
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'),
                'label' => 'Promo Kit Available',
                'choices' => array(
                    'No' => 0,
                    'Yes' => 1,
                ),
            ))
            ->getForm()
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\PopItem'
        ));
    }
}
