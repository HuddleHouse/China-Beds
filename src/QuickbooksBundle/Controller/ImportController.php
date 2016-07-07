<?php

namespace QuickbooksBundle\Controller;

//require "/Users/work/Sites/resume/vendor/consolibyte/quickbooks/QuickBooks.php";

use QuickbooksBundle\QuickbooksBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PDO;

/**
 * @Route("/import")
 */
class ImportController extends Controller
{

    protected $connection;

    public function __construct() {
        $this->connection = new PDO('mysql:host=localhost;dbname=quick;charset=utf8', 'quick', 'quick');
    }

    /**
     * @Route("/", name="qb_import_home")
     */
    public function qbImportHome()
    {
        return $this->render('@Quickbooks/Import/index.html.twig'
        );
    }
    
    
    /**
     * @Route("/items", name="qb_import_items")
     */
    public function qbImportItems()
    {
        $statement = $this->connection->prepare('select * from ItemQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        
        foreach($results as $result) {
            $i = 1;
        }

        return JsonResponse::create(true);
    }
}
