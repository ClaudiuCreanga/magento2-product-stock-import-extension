<?php
/**
 * Limesharp_Import extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Limesharp
 * @package   Limesharp_Import
 * @copyright 2016 Limesharp
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */

namespace Limesharp\Import\Cron\Stock;

use Limesharp\Import\Cron\AbstractCron;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Limesharp\Import\Logger\Logger;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class ProcessStockSave
 * @package Limesharp\Import\Cron\Stock
 */
class ProcessStockSave extends AbstractCron
{
    /**
     * @var ProductFactory
     */
    public $productFactory;

    /**
     * @var StockItemInterfaceFactory
     */
    public $stockFactory;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var array
     */
    public $fieldsHeader = array(
        'Product Code',
        'Available To Sell'
    );

    /**
     * @var int
     */
    private $row;

    /**
     * ProcessStockSave constructor.
     * @param ProductFactory $productFactory
     * @param StockItemInterfaceFactory $stockFactory
     * @param Logger $logger
     */
    public function __construct(
        ProductFactory $productFactory,
        StockItemInterfaceFactory $stockFactory,
        Logger $logger
    ) {
        $this->productFactory = $productFactory;
        $this->stockFactory = $stockFactory;
        $this->logger = $logger;
    }

    /**
     *
     * Processes stock data
     *
     * @param array $data
     * @param int $row
     * @return void
     *
     */
    public function updateStockOnSave($data, $row)
    {
        $productData = array_combine($this->fieldsHeader, $data);
        if (!$productData['Product Code'] || $productData['Available To Sell'] === false || $productData['Available To Sell'] === null) {
            $this->setErrors("No valid data on ".$row);
            return;
        }
        $currentProduct = $this->productFactory->create()->loadByAttribute('sku', $productData['Product Code']);
        if(!$currentProduct){
            $this->logger->info("Product ".$productData['Product Code']." does not exist.");
        } else {
            $this->updateStock($currentProduct,$productData);
            $this->logger->info("Product ".$productData['Product Code']." Stock ".$productData['Available To Sell']);
        }
    }

    /**
     *
     * Update stock for products
     *
     * @param $currentProduct Object
     * @param $product_data Array
     * @return void
     *
     */
    private function updateStock($currentProduct,$productData)
    {
        $stockItem = $this->stockFactory->create()->load($currentProduct->getid(), 'product_id');
        $inStock = ($productData['Available To Sell'] > 0) ? 1 : 0;
        $stockItem->setIsInStock($inStock);
        $stockItem->setData('qty', $productData['Available To Sell']);
        $this->saveStock($stockItem,$productData['Product Code']);
    }

    /**
     * @param object $product
     * @param array $productData
     */
    public function saveStock($stockItem, $productData)
    {

        //validate returns true if everything is ok and an array with the errors otherwise
        try{
            $stockItem->save();
            $this->logger->info("Saved stock sku ".$productData);
        } catch(Exception $e){
            $this->setErrors($e->getMessage());
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param int $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }
}
