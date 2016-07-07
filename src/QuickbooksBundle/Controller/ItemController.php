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

    /**
     * @Route("/get-all-items", name="qb_get_all_items")
     */
    public function qbGetAllItems()
    {
        $statement = $this->connection->prepare('select * from ItemQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        
        return $results;
    }
}
