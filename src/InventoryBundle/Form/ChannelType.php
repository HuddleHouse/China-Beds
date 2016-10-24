<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ChannelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Site Name', 'required' => false))
            ->add('url', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Site URL'))
            ->add('frontLogo',  FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Front Page Logo', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('fbLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Facebook Link', 'required' => false))
            ->add('twLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Twitter Link', 'required' => false))
            ->add('instaLink',  TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Instagram Link', 'required' => false))
            ->add('frontSliderOne', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'First Slider', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontSliderTwo', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Second Slider', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontSliderThree', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Third Slider', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontFooterOne', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'First Footer Box', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontFooterTwo', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Second Footer Box', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontFooterThree', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Third Footer Box', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('frontFooterText', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Footer Text', 'required' => false))

            ->add('faqWarrantyPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Warranty Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('faqUnpackingPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Unpacking Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('faqSupportPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Support Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('faqMaintenancePic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Maintenance Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('faqContactPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Contact Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('faqTCPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Terms & Conditions Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))

            ->add('pFMemoryFoamPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Memory Foam Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFSidePic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Side Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFRenewResourcewPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Renewable Resource Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFsocsPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Semi-Open Cell Structure Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFpboPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Plant Based Oils Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFBCharcoalPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Bamboo Charcoal Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFBFibersPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Bamboo Fibers Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFSilkPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Silk Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFAloeVeraPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Aloe Vera Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFCertifiedPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Certified Foam Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('pFTexStandPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'OEKO TEX STANDARD Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))

            ->add('retailHeaderPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Header Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('retailFirstPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'First Row Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('retailSecondPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Second Row Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('retailThirdPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Third Row Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))
            ->add('retailFourthPic', FileType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom: 10px'), 'label' => 'Fourth Row Picture', 'label_attr' => array('class' => 'file-input'), 'required' => false, 'data_class' => null))


        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Channel'
        ));
    }
}
