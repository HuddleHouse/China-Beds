<?php

namespace InventoryBundle\Repository;
use InventoryBundle\Entity\Channel;
use AppBundle\Entity\User;

/**
 * ChannelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChannelRepository extends \Doctrine\ORM\EntityRepository
{

    public function getProductArrayForChannel(Channel $channel,User $user)
    {
        // select all products that are in the channel
        // get all product variants for it.

        $em = $this->getEntityManager();
        $product_channels = $em->getRepository('InventoryBundle:ProductChannel')->findBy(array('channel' => $channel));
        $product_data = array();
        $user_price_groups = $user->getPriceGroupsString();

        foreach($product_channels as $product_channel) {
            $product = $product_channel->getProduct();

            // get first image url
            $image_url = '/';
            foreach($product->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            // format needed data to array
            $product_array = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'sku' => $product->getSku(),
                'category' => $product->getCategory()->getName(),
                'category_id' => $product->getCategory()->getId(),
                'path' => $image_url,
                'quantity' => 0
            );

            $warehouse_ids = "(".$user->getWarehouse1()->getId().','.$user->getWarehouse2()->getId().','.$user->getWarehouse3()->getId().')';

            // get only the product variants the user has a price in their price group for
            $connection = $em->getConnection();
            $statement = $connection->prepare("
select *, v.id as variant_id, min(p.price/100) as price, 
	(select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_inventory i
			where i.warehouse_id in ".$warehouse_ids." 
			and i.product_variant_id = p.product_variant_id) as inventory
	from product_variant v 
		left join price_group_prices p 
			on p.product_variant_id = v.id
		where v.product_id = :product_id
			and p.price_group_id in (".$user_price_groups.") 
		group by variant_id;");
            $statement->bindValue('product_id', $product->getId());
            $statement->execute();
            $variants = $statement->fetchAll();

            $product_array['variants'] = $variants;

            $product_data[] = $product_array;
        }

        return $product_data;
    }
}
