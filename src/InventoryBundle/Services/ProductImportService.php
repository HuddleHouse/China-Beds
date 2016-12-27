<?php

namespace InventoryBundle\Services;

use Doctrine\ORM\EntityManager;
use InventoryBundle\Entity\ProductChannel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\ProductImage;
use InventoryBundle\Entity\ProductSpecification;
use InventoryBundle\Entity\ProductAttribute;
use InventoryBundle\Entity\Attribute;
use InventoryBundle\Entity\Specification;
use InventoryBundle\Form\ProductType;
use QuickbooksBundle\Controller\ItemController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use InventoryBundle\Entity\ProductVariant;
use InventoryBundle\Entity\ProductCategory;
class ProductImportService
{
    private $em;
   
    /**
     * SettingsService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

    }
    
    public function import($products, $channelId, $categoryId) {
	
	$em = $this->em;
	
	
	$channelEntity =  $em->getRepository('InventoryBundle:Channel')->find($channelId);
	$categoryEntity =  $em->getRepository('InventoryBundle:Category')->find($categoryId);
	
	if (count($products)>0) {
		
		foreach ($products as $product) {
			
			$productEntity = new Product();
			$productEntity->setName($product->name);
			$productEntity->setDescription($product->description);
			$productEntity->setHideBackend($product->hide_backend);
			$productEntity->setMetaDescription($product->metaDescription);
			$productEntity->setShortDescription($product->shortDescription);
			$productEntity->setSku($product->sku);
			$productEntity->setActive($product->active);
			$productEntity->setTagline($product->tagline);
			$productEntity->setFrontHeadline($product->frontHeadline);
			$productEntity->setHideFrontend($product->hideFrontend);
			$productEntity->setPromoKitAvailable($product->promoKitAvailable);
			$productEntity->setListId($product->listId);
			$productEntity->setHideBackend($product->hide_backend);
			
			
			if ($channelEntity) {
				$productChannel = new ProductChannel();
				$productChannel->setChannel($channelEntity);
				$productChannel->setProduct($productEntity);
				$em->persist($productChannel);
			}
			
			
			if ($categoryEntity) {
				$productCategory = new ProductCategory();
				$productCategory->setCategory($categoryEntity);
				$productCategory->setProduct($productEntity);
				$em->persist($productCategory);
			}
			
			$variants = $product->variants;
			
			if (count($variants)>0) {
			      foreach ($variants as $variant) {
				      $variantEntity = new ProductVariant();
				      $variantEntity->setName($variant->name);
				      $variantEntity->setSku($variant->sku);
				      $variantEntity->setWeight($variant->weight);
				      $variantEntity->setFedexDimensions($variant->dimensions);
				      $variantEntity->setMsrp($variant->msrp);
				      $variantEntity->setProduct($productEntity);
				      
				      $em->persist($variantEntity);
			      }
			}
			
			$specifications = $product->specifications;
			
			if (count($specifications)>0) {
			      foreach ($specifications as $specification) {
					$specificationName = $specification->name;
					$specificationCode = $specification->code;
					
					$specificationEntity = $em->getRepository('InventoryBundle:Specification')->findOneBy(array('name'=>$specificationName, 'code'=>$specificationCode));
					if (!$specificationEntity) {
						$specificationEntity = new Specification();
						$specificationEntity->setName($specificationName);
						$specificationEntity->setCode($specificationCode);
						$em->persist($specificationEntity);
					}
					
					$productSpecification = new ProductSpecification();
					$productSpecification->setProduct($productEntity);
					$productSpecification->setSpecification($specificationEntity);
					$em->persist($productSpecification);
					
					
					
			      }
			}
			
			$attributes = $product->attributes;
			
			if (count($attributes)>0) {
				foreach ($attributes as $attribute) {
					$attributeName = $attribute->name;
					$attributeCode = $attribute->code;
					$attributeAltTag = $attribute->alt_tag;
					
					$attributeEntity = $em->getRepository('InventoryBundle:Attribute')->findOneBy(array('name'=>$attributeName, 'code'=>$attributeCode));
					if (!$attributeEntity) {
						$attributeEntity = new Attribute();
						$attributeEntity->setName($attributeName);
						$attributeEntity->setCode($attributeCode);
						$em->persist($attributeEntity);
					}
					
					$productAttribute = new ProductAttribute();
					$productAttribute->setProduct($productEntity);
					$productAttribute->setAttribute($attributeEntity);
					$em->persist($productAttribute);
					
					
					
				}
			}
			$images = $product->images;
			
			if (count($images)>0) {
			
				foreach ($images as $image) {
					
					$path = explode('/',$image->path);
					$imageName =  end($path);
					$detailImage = $image->detailImage;
					
					if (file_exists($image->path)) {
						$data = file_get_contents($image->path);
						file_put_contents($this->get('kernel')->getRootDir() . '/../web/uploads/products/'.$imageName, $data);
						
						$productImage = new ProductImage();
						$productImage->setPath($imageName);
						$productImage->setDetailImage($detailImage);
						$productImage->setProduct($productEntity);
						$em->persist($productImage);
					}
					
				}
			
			}
			
			$em->persist($productEntity);
			$em->flush();
		}	
	}
	
    }
}