<?php

namespace QuickbooksBundle\Controller;

//require "/Users/work/Sites/resume/vendor/consolibyte/quickbooks/QuickBooks.php";

use QuickbooksBundle\QuickbooksBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use PDO;

/**
 * @Route("/api/qb")
 */
class ItemController extends Controller
{

    protected $connection;

    public function __construct() {
        $this->connection = new PDO('mysql:host=localhost;dbname=quick;charset=utf8', 'quick', 'quick');
    }

    
    public function qbQuantityForWarehouse($warehouseListID)
    {
        $statement = $this->connection->prepare('select sum(QuantityOnHand) as quantity, sum(QuantityOnPurchaseOrders) as po_quantity from ItemSitesQueryRs
	where InventorySiteRefListID = :id');
        $statement->bindParam(':id', $warehouseListID);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_OBJ);
        
        return $results;
    }
}
