<?php

namespace QuickbooksBundle\Controller;

//require "/Users/work/Sites/resume/vendor/consolibyte/quickbooks/QuickBooks.php";

use InventoryBundle\Entity\PopItem;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Warehouse;
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
        $em = $this->getDoctrine()->getManager();

        foreach($results as $result) {
            $product = $em->getRepository('InventoryBundle:Product')->findOneBy(array('list_id' => $result->ListID));
            if($product == null) {
                $newProduct = new Product();
                $newProduct->setName($result->Name);
                $newProduct->setDescription($result->SalesDesc);
                $newProduct->setMetaDescription($result->SalesDesc);
                $newProduct->setShortDescription($result->SalesDesc);
                $newProduct->setSku($result->EditSequence);
                $newProduct->setListId($result->ListID);
                $newProduct->setActive(1);
                $newProduct->setFrontHeadline($result->Name);
                $em->persist($newProduct);
            }
        }

        $em->flush();
        return JsonResponse::create(true);
    }

    /**
     * @Route("/remove-items", name="qb_import_remove_items")
     */
    public function qbImportRemoveItems()
    {
        $statement = $this->connection->prepare('select * from ItemQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        foreach($products as $result) {
            if($result->getListId() != null) {
                $em->remove($result);
                $em->flush();
            }
        }

        return JsonResponse::create(true);
    }

    /**
     * @Route("/warehouses", name="qb_import_warehouses")
     */
    public function qbImportWarehouses()
    {
        $statement = $this->connection->prepare('select * from InventorySiteQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $em = $this->getDoctrine()->getManager();

        foreach($results as $result) {
            $warehouse = $em->getRepository('InventoryBundle:Warehouse')->findOneBy(array('list_id' => $result->ListID));
            if($warehouse == null) {
                $newWarehouse = new Warehouse();
                $newWarehouse->setName($result->Name);
                $newWarehouse->setDescription($result->SiteDesc);
                $newWarehouse->setContact($result->Contact);
                $newWarehouse->setListId($result->ListID);
                $em->persist($newWarehouse);
            }
        }

        $em->flush();
        return JsonResponse::create(true);
    }

    /**
     * @Route("/remove-warehouses", name="qb_import_remove_warehouses")
     */
    public function qbImportRemoveWarehouses()
    {
        $statement = $this->connection->prepare('select * from ItemQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        foreach($products as $result) {
            if($result->getListId() != null) {
                $em->remove($result);
                $em->flush();
            }
        }

        return JsonResponse::create(true);
    }


    /**
     * @Route("/pop-items", name="qb_import_pop_items")
     */
    public function qbImportPopItems()
    {
        $param = "POP";
        $statement = $this->connection->prepare('select * from ItemInventoryQueryRs where ManufacturerPartNumber = :pop');
        $statement->bindParam(':pop', $param);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $em = $this->getDoctrine()->getManager();

        foreach($results as $result) {
            $popItem = $em->getRepository('InventoryBundle:PopItem')->findOneBy(array('list_id' => $result->ListID));
            if($popItem == null) {
                $newPopItem = new PopItem();
                $newPopItem->setName($result->Name);
                $newPopItem->setDescription($result->SalesDesc);
                $newPopItem->setListId($result->ListID);
                $newPopItem->setActive(1);
                $em->persist($newPopItem);
            }
        }

        $em->flush();
        return JsonResponse::create(true);
    }

    /**
     * @Route("/remove-pop-items", name="qb_import_remove_pop_items")
     */
    public function qbImportRemovePopItems()
    {
        $statement = $this->connection->prepare('select * from ItemQueryRs');
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();
        foreach($products as $result) {
            if($result->getListId() != null) {
                $em->remove($result);
                $em->flush();
            }
        }

        return JsonResponse::create(true);
    }
}
