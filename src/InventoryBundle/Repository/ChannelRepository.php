<?php

namespace InventoryBundle\Repository;
use InventoryBundle\Entity\Channel;
use AppBundle\Entity\User;
use WarehouseBundle\Entity\Warehouse;

/**
 * ChannelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChannelRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Channel $channel
     * @param User $user
     * @param Warehouse|null $warehouse
     * @param null $categories
     * @param null $include_closeouts
     * @return array
     */
    public function getProductArrayForChannel(Channel $channel, User $user, Warehouse $warehouse = null, $categories = null, $include_closeouts = null)
    {
        // select all products that are in the channel
        // get all product variants for it.
        $em = $this->getEntityManager();
        $product_channels = $em->getRepository('InventoryBundle:ProductChannel')->findBy(array('channel' => $channel));
        $product_data = array();
        $user_price_groups = $user->getPriceGroupsString();

        //loop through all products
        foreach($product_channels as $product_channel) {
            $product = $product_channel->getProduct();

            if ( $product->getHideBackEnd() ) { continue; }
            if ( !$product->getActive() ) { continue; }

            // get first image url
            $image_url = '/';
            foreach($product->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            // check if Product is in closeout and make string to be used in query
            $cat_ids = '';
            $is_closeout = 0;
            $cat_count = 0;
            foreach($product->getCategories() as $cat) {
                $cat_ids .= $cat->getCategory()->getId() . ' ';
                $cat_count++;
                $cat_name = $cat->getCategory()->getName();
                if(strtoupper($cat_name) == 'CLOSEOUT' && $include_closeouts != null)
                    $is_closeout = 1;
            }

            // format needed data to array
            $product_array = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'sku' => $product->getSku(),
                'cat_ids' => $cat_ids,
                'path' => $image_url,
                'cat_name' => $cat_name,
                'quantity' => 0
            );

            if($user_price_groups != false) {
                // get only the product variants the user has a price in their price group for
                $connection = $em->getConnection();
                $statement = $connection->prepare("
select *, v.id as variant_id, TRUNCATE(min(p.price/100), 2) as cost, 0 as inventory, 'variant' as type
	from product_variant v 
		left join price_group_prices p 
			on p.product_variant_id = v.id
		where v.product_id = :product_id
			and p.price_group_id in (".$user_price_groups.") 
		group by variant_id;");
                $statement->bindValue('product_id', $product->getId());
                $statement->execute();
                $variants = $statement->fetchAll();

                if($warehouse != null)
                    $warehouse_ids = "(".$warehouse->getId().')';
                else {
                    $warehouses = [0];
                    foreach($user->getWarehouses() as $warehouse) {
                        $warehouses[] = $warehouse->getId();
                    }
//                    $warehouses = [0];
//                    if ( $user->getWarehouse1() ) {
//                        $warehouses[] = $user->getWarehouse1()->getId();
//                    }
//                    if ( $user->getWarehouse2() ) {
//                        $warehouses[] = $user->getWarehouse2()->getId();
//                    }
//                    if ( $user->getWarehouse3() ) {
//                        $warehouses[] = $user->getWarehouse3()->getId();
//                    }
                    $warehouse_ids = "(" . implode(',', $warehouses) . ')';
                }

                foreach($variants as $key => $variant) {
                    $quantity = 0;
                    //loop through the variants to get their inventory quantities and each warehousesinventory

                    if($is_closeout == 0) {
                        $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_inventory i
			where i.warehouse_id in ".$warehouse_ids." 
		and i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $quantity_data = $statement->fetch();
                        $quantity += (int)$quantity_data['quantity'];

                        $statement = $connection->prepare("
select i.quantity, i.warehouse_id
		from warehouse_inventory i
			where i.warehouse_id in ".$warehouse_ids." 
		and i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $warehouse_data = $statement->fetchAll();
                    }
                    else {
                        $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_inventory i
			where i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $quantity_data = $statement->fetch();
                        $quantity += (int)$quantity_data['quantity'];

                        $statement = $connection->prepare("
select i.quantity, i.warehouse_id
		from warehouse_inventory i
			where i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $warehouse_data = $statement->fetchAll();
                    }

                    $variants[$key]['inventory'] = $quantity;
                    $variants[$key]['warehouse_data'] = $warehouse_data;
                    $variants[$key]['open_item'] = (strtoupper($variant['fedex_dimensions']) == 'OPEN');
                }

                $product_array['variants'] = $variants;

                if($categories == null)
                    $product_data[] = $product_array;
                else {
                    foreach($product->getCategories() as $cat)
                        if(in_array($cat->getName(), $categories))
                            $product_data[] = $product_array;
                }
            }
        }

        return $product_data;
    }

    public function getProductArrayForChannel2(Channel $channel, User $user, Warehouse $warehouse = null, $categories = null)
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

            $cat_ids = '';

            foreach($product->getCategories() as $cat)
                $cat_ids .= $cat->getCategory()->getId() . ' ';

            // format needed data to array
            $product_array = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'sku' => $product->getSku(),
                'cat_ids' => $cat_ids,
                'path' => $image_url,
                'quantity' => 0
            );

            // Fix this. Would error when a user doesn't have three warehouses.
            if($warehouse != null)
                $warehouse_ids = "(".$warehouse->getId().')';
            else
                $warehouse_ids = "(".$user->getWarehouse1()->getId().','.$user->getWarehouse2()->getId().','.$user->getWarehouse3()->getId().')';



//            if($user_price_groups != false) {
//                // get only the product variants the user has a price in their price group for
//                $connection = $em->getConnection();
//                $statement = $connection->prepare("
//select *, v.id as variant_id, min(p.price/100) as cost,
//	(select coalesce(sum(i.quantity), 0) as quantity
//		from warehouse_inventory i
//			where i.warehouse_id in ".$warehouse_ids."
//			and i.product_variant_id = p.product_variant_id) as inventory
//	from product_variant v
//		left join price_group_prices p
//			on p.product_variant_id = v.id
//		where v.product_id = :product_id
//			and p.price_group_id in (".$user_price_groups.")
//		group by variant_id;");
//                $statement->bindValue('product_id', $product->getId());
//                $statement->execute();
//                $variants = $statement->fetchAll();

                $product_array['variants'] = $variants;

                if($categories == null)
                    $product_data[] = $product_array;
                else {
                    foreach($product->getCategories() as $cat)
                        if(in_array($cat->getName(), $categories))
                            $product_data[] = $product_array;
                }
//            }
        }

        return $product_data;
    }

    /**
     * @param Channel $channel
     * @param User $user
     * @param null $include_closeouts
     * @return array
     */
    public function getAllProductsArrayForChannel(Channel $channel, User $user, $include_closeouts = null)
    {
        // select all products that are in the channel
        // get all product variants for it.
        $em = $this->getEntityManager();
        $product_channels = $em->getRepository('InventoryBundle:ProductChannel')->findBy(array('channel' => $channel));
        $product_data = array();
        $user_price_groups = $user->getPriceGroupsString();

        //loop through all products
        foreach($product_channels as $product_channel) {
            $product = $product_channel->getProduct();

            if ( $product->getHideBackEnd() ) { continue; }
            if ( !$product->getActive() ) { continue; }

            // get first image url
            $image_url = '/';
            foreach($product->getImages() as $image) {
                $image_url .= $image->getWebPath();
                break;
            }

            // check if Product is in closeout and make string to be used in query
            $cat_ids = '';
            $is_closeout = 0;
            $cat_count = 0;
            foreach($product->getCategories() as $cat) {
                $cat_ids .= $cat->getCategory()->getId() . ' ';
                $cat_count++;
                $cat_name = $cat->getCategory()->getName();
                if(strtoupper($cat_name) == 'CLOSEOUT' && $include_closeouts != null)
                    $is_closeout = 1;
            }

            // format needed data to array
            $product_array = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'sku' => $product->getSku(),
                'cat_ids' => $cat_ids,
                'path' => $image_url,
                'quantity' => 0
            );

            if($user_price_groups != false) {
                // get only the product variants the user has a price in their price group for
                $connection = $em->getConnection();
                $statement = $connection->prepare("
select *, v.id as variant_id, TRUNCATE(min(p.price/100), 2) as cost, 0 as inventory, 'variant' as type
	from product_variant v
		left join price_group_prices p
			on p.product_variant_id = v.id
		where v.product_id = :product_id
			and p.price_group_id in (".$user_price_groups.")
		group by variant_id");
                $statement->bindValue('product_id', $product->getId());
                $statement->execute();
                $variants = $statement->fetchAll();

                if($warehouse != null)
                    $warehouse_ids = "(".$warehouse->getId().')';
                else
                    $warehouse_ids = "(".$user->getWarehouse1()->getId().','.$user->getWarehouse2()->getId().','.$user->getWarehouse3()->getId().')';

                foreach($variants as $key => $variant) {
                    $quantity = 0;
                    //loop through the variants to get their inventory quantities and each warehousesinventory

                    if($is_closeout == 0) {
                        $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_inventory i
			where i.warehouse_id in ".$warehouse_ids."
		and i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $quantity_data = $statement->fetch();
                        $quantity += (int)$quantity_data['quantity'];

                        $statement = $connection->prepare("
select i.quantity, i.warehouse_id
		from warehouse_inventory i
			where i.warehouse_id in ".$warehouse_ids."
		and i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $warehouse_data = $statement->fetchAll();
                    }
                    else {
                        $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_inventory i
			where i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $quantity_data = $statement->fetch();
                        $quantity += (int)$quantity_data['quantity'];

                        $statement = $connection->prepare("
select i.quantity, i.warehouse_id
		from warehouse_inventory i
			where i.product_variant_id = :product_variant_id");
                        $statement->bindValue('product_variant_id', $variant['product_variant_id']);
                        $statement->execute();
                        $warehouse_data = $statement->fetchAll();
                    }

                    $variants[$key]['inventory'] = $quantity;
                    $variants[$key]['warehouse_data'] = $warehouse_data;
                }

                $product_array['variants'] = $variants;

                if($categories == null)
                    $product_data[] = $product_array;
                else {
                    foreach($product->getCategories() as $cat)
                        if(in_array($cat->getName(), $categories))
                            $product_data[] = $product_array;
                }
            }
        }

        return $product_data;
    }
}
