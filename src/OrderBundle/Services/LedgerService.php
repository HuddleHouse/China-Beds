<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 9/27/16
 * Time: 14:56
 */

namespace OrderBundle\Services;

use AppBundle\Entity\User;
use InventoryBundle\Entity\Channel;
use Nacha\Batch;
use Nacha\File;
use Nacha\Record\DebitEntry;
use OrderBundle\Entity\Ledger;
use Doctrine\Bundle\DoctrineBundle\Registry;
use phpseclib\Net\SFTP;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LedgerService
 * @package OrderBundle\Services
 */
class LedgerService
{
    protected $container;

    /**
     * LedgerService constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    protected function getDoctrine()
    {
        if (!$this->container->has('doctrine'))
            throw new \LogicException('The DoctrineBundle is not registered in your application.');

        return $this->container->get('doctrine');
    }

    /**
     * @param $amount
     * @param User $submittedForUser
     * @param User $submittedByUser
     * @param Channel $channel
     * @param null $description
     * @param string $type check \OrderBundle\Entity\Ledger for the constants that $type accepts, it will be 'Credit' if left null
     * @param null $typeId if this is not a credit request, add the id of the entity that should count towards credit
     *                     however, ledger entities are inherently credit, so no id is needed
     * @param bool|false $returnArray return as an array instead of as an entity object if true
     * @return Ledger
     * @throws \Exception if updating the entities fails
     */
    public function newEntry($amount, User $submittedForUser, User $submittedByUser, Channel $channel, $description = null, $type = 'Credit', $typeId = null, $returnArray = false, $approved = false, $flush = true) {
        $em = $this->getDoctrine()->getManager();

        $ledger = new Ledger();
        $ledger->setAmountRequested($amount);
        $ledger->setSubmittedForUser($submittedForUser);
        $ledger->setSubmittedByUser($submittedByUser);
        $ledger->setChannel($channel);
        if($type == Ledger::TYPE_CREDIT || $type == Ledger::TYPE_PAYMENT)
            $ledger->setAchRequested(false);
        $ledger->setDescription($description);
        $ledger->setType($type);

        //remove these if we ever implement credit approval
        $ledger->setAmountCredited($amount);
        $ledger->setDatePosted(new \DateTime());

        //put the ledger records where they belong
        $ledger->setSubmittedByUser($submittedByUser);
        $ledger->setSubmittedForUser($submittedForUser);
        $ledger->setChannel($channel);

        $ledger->setIsApproved($approved);

        switch($type) {
            case 'Order':
                $order = $em->getRepository('OrderBundle:Orders')->find($typeId);
                $ledger->setOrder($order);
                break;
            case 'Rebate':
                $rebateSubmission = $em->getRepository('InventoryBundle:RebateSubmission')->find($typeId);
                $ledger->setRebateSubmission($rebateSubmission);
                $ledger->setIsArchived(true);
                $ledger->setRebateSubmission($ledger);
                break;
            case 'Warranty':
                $warrantyClaim = $em->getRepository('InventoryBundle:WarrantyClaim')->find($typeId);
                $ledger->setWarrantyClaim($warrantyClaim);
                $ledger->setIsArchived(true);
                $ledger->setWarrantyClaim($warrantyClaim);
                break;
        }



        if ( $flush ) {
            $em->persist($ledger);
            $em->flush();
        }

        if($returnArray)
            return $ledger->toArray();

        return $ledger;
    }

    public function generatePendingNACHAFile($filename) {


        $file = new File();
        $file->getHeader()->setPriorityCode(1)
            ->setImmediateDestination('051000017')
            ->setImmediateOrigin('1275135100')
            ->setFileCreationDate(date("ymd"))
            ->setFileCreationTime(date("Hi"))
            ->setFormatCode('1')
            ->setImmediateDestinationName('BANK OF AMERICA')
            ->setImmediateOriginName('CHINA BEDS DIRECT LLC')
            ->setReferenceCode(date("YmdHis"));

        foreach($this->getDoctrine()->getManager()->getRepository('InventoryBundle:Channel')->findAll() as $channel) {
            // Create a batch and add some entries
            $batch = new Batch();
            $batch->getHeader()
                ->setCompanyId($channel->getAchCompanyId())
                ->setCompanyName($channel->getAchCompanyName())
                ->setCompanyEntryDescription('ECHECKPAY')
                ->setEffectiveEntryDate(date('ymd'))
                ->setOriginatingDFiId($channel->getAchOriginatingDfi());

            foreach($this->getDoctrine()->getManager()->getRepository('OrderBundle:Ledger')->findBy(['type' => Ledger::TYPE_PAYMENT, 'achRequested' => false, 'channel' => $channel]) as $entry) {
                $receiving_dfi = substr($entry->getSubmittedForUser()->getAchRoutingNumber(), 0, 8);
                $check_digit = substr($entry->getSubmittedForUser()->getAchRoutingNumber(), 8, 1);
                $batch->addDebitEntry(
                    (new DebitEntry())
                        ->setTransactionCode(27)
                        ->setReceivingDfiId($receiving_dfi)
                        ->setCheckDigit($check_digit)
                        ->setDFiAccountNumber($entry->getSubmittedForUser()->getAchAccountNumber())
                        ->setAmount($entry->getAmountRequested())
                        ->setIndividualId(str_pad($entry->getSubmittedForUser()->getId(), 10, 0, STR_PAD_LEFT))
                        ->setIdividualName($entry->getSubmittedForUser()->getCompanyName() ? $entry->getSubmittedForUser()->getCompanyName() : $entry->getSubmittedForUser()->getName())
                        ->setDiscretionaryData('S')
                        ->setAddendaRecordIndicator(0)
                        ->setTraceNumber(substr(time(), -8), 15)
                );
            }

            $file->addBatch($batch);
        }

        $output = (string)$file;
        file_put_contents($filename, $output);
    }

    public function uploadNACHAFile($filename) {
        switch($this->container->get('kernel')->getEnvironment()) {
            default:
            case 'dev':
                $host = $this->container->getParameter('nacha.uat.host');
                $user = $this->container->getParameter('nacha.uat.user');
                $pass = $this->container->getParameter('nacha.uat.pass');

        }
        $sftp = new SFTP($host);
        if ( !$sftp->login($user, $pass) ) {
            die('Login Failed');
        }

        echo $sftp->put('/' . basename($filename), $filename);

        return true;

    }
}