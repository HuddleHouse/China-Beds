<?php

namespace InventoryBundle\Repository;

use InventoryBundle\Entity\Channel;

/**
 * PopItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PopItemRepository extends \Doctrine\ORM\EntityRepository
{

    public function getAllPopItemsArrayForCart(Channel $channel, $path = null)
    {
        $em = $this->getEntityManager();
        $pop = $em->getRepository('InventoryBundle:PopItem')->findBy(['channel' => $channel, 'is_hide_on_order' => 0, 'active' => 1]);
        $data = array();

        foreach($pop as $popitem) {
            $connection = $em->getConnection();
            $statement = $connection->prepare("
select coalesce(sum(i.quantity), 0) as quantity
		from warehouse_pop_inventory i
		where i.pop_item_id = :pop_item_id");
            $statement->bindValue('pop_item_id', $popitem->getId());
            $statement->execute();
            $quantity_data = $statement->fetch();
            $quantity = (int)$quantity_data['quantity'];

            $data[] = array(
                'id' => $popitem->getId(),
                'cost' => $popitem->getPricePer(),
                'name' => $popitem->getName(),
                'description' => $popitem->getDescription(),
                'picture' => $path . $popitem->getPath(),
                'type' => 'pop',
                'inventory' => $quantity //get actually inventory one day
            );
        }
        return $data;
    }

}
