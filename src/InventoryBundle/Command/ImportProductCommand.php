<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
   eg: php bin/console milli-import-products --channelId=1 --categoryId=1 --file=products-20161227184927.json
 */

namespace InventoryBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('milli-import-products')
            ->setDescription('Run the product importer')
            ->addOption('channelId', null, InputOption::VALUE_REQUIRED, 'Channel Id for product')
            ->addOption('categoryId', null, InputOption::VALUE_REQUIRED, 'Category Id for product')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Json file for product');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
	
	$channelId = $input->getOption('channelId');
	$categoryId = $input->getOption('categoryId');
	$fileName =  $input->getOption('file');
	
	$filePath = $this->getContainer()->get('kernel')->getRootDir() . '/../app/products/'.$fileName;
	
	if (file_exists($filePath)) {
		
		$products = json_decode(file_get_contents($filePath));
		
		if ($channelId>0 && $categoryId>0) {
			$cron = $this->getContainer()->get('product_import_service');
			$text = $cron->import($products, $channelId, $categoryId);
			$output->writeln('Products imported successfully');
		} else {
			$output->writeln('Channelid and categoryid required');
		}
		
        } else {
		  $output->writeln('Json file you provided is not exists');
        }
    }
}
