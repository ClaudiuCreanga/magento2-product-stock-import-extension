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

namespace Limesharp\Import\Cron\Products;

use Braintree\Exception;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Limesharp\Import\Logger\Logger;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Limesharp\Import\Cron\AbstractCron;

/**
 * Class ProcessProducts
 * @package Limesharp\Import\Cron\Products
 */
class ProcessProducts extends AbstractCron
{
    /**
     * @var ProductFactory
     */
    public $productFactory;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var ProductLinkInterface
     */
    protected $productLinks;

    /**
     * @var array of hard coded attributes
     */
    public $hardCodedAttributes = array(
        "setAttributeSetId" => "4",
        "setWebsiteId" => "1"
    );

    /**
     * @var int
     */
    private $row;

    /**
     * Url finder
     *
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * ProcessProducts constructor.
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductLinkInterfaceFactory $productLinks
     * @param Logger $logger
     */
    public function __construct(
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        ProductLinkInterfaceFactory $productLinks,
        Logger $logger
    ) {
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->productLinks = $productLinks;
        $this->logger = $logger;
    }

    /**
     * Decide if we want to create a product or update a product
     * @param array $productData
     */
    public function manageTypeOfProducts($productData, $row)
    {
        $this->setRow($row);
        $currentProduct = $this->productFactory->create()->loadByAttribute("sku",$productData["setSku"]);
        if(!$currentProduct){
            $this->createproduct($productData);
        } else {
            $this->updateProduct($currentProduct,$productData);
        }
    }

    /**
     * Create the product from hardcoded array and file data
     * @param array $productData
     */
    public function createproduct($productData)
    {
        $this->logger->info("Starting creating product with sku ".$productData["setSku"]);
        $newProduct = $this->productFactory->create();
        $newProductWithAttributesFromFile = $this->setAttributesFromFile($newProduct,$productData);
        $newProductWithHardCodedAttributes = $this->setHardcodedAttributes($newProductWithAttributesFromFile);

        $this->saveProduct($newProductWithHardCodedAttributes,$productData);
    }

    /**
     * Update the product from file
     * @param object $currentProduct
     * @param array $productData
     */
    public function updateProduct($currentProduct, $productData)
    {
        $newProductWithAttributesFromFile = $this->setAttributesFromFile($currentProduct,$productData);
        $this->saveProduct($newProductWithAttributesFromFile,$productData);
    }

    /**
     * For each attribute in the file, update the product object. Attributes start with "set" so we can call them as variables. A special case for linked products (related, upsell, crossels)
     * @param object $product
     * @param array $productData
     * @return mixed
     */
    public function setAttributesFromFile($product, $productData)
    {
        $linkDataAll = [];
        $product->setStoreId('0');
        foreach($productData as $key => $value){
            if ($value) {
                if ($key == "setRelated" || $key == "setUpsell" || $key == "setCrosssell") {
                    $linkDataAll = array_merge($linkDataAll, $this->setLinkedProducts($key, $value, $productData));
                    if ($linkDataAll) {
                        $product->setProductLinks($linkDataAll);
                    }
                    continue;
                }
                if ($key == "setQty") {
                    $product->setStockData([
                        'use_config_manage_stock' => 1,
                        'qty' => $value,
                        'is_qty_decimal' => 0,
                        'is_in_stock' => $productData["setIsInStock"]
                    ]);
                    continue;
                }
                if ($key == "setIsInStock") {
                    continue;
                }
                if ($key == "setCategoryIds") {
                    $categoryIds = explode(",",$value);
                    $product->setCategoryIds($categoryIds);
                    continue;
                }
                if ($key == "setTaxClassId") {
                    if ($value == "Taxable Goods - Printed Books") {
                        $product->setTaxClassId(5);
                    } elseif ($value == "Taxable Good Gift") {
                        $product->setTaxClassId(4);
                    } elseif ($value == "Taxable Goods") {
                        $product->setTaxClassId(2);
                    } else {
                        $product->setTaxClassId(0);
                    }
                    continue;
                }
                $product->$key($value);
            }
        }

        return $product;
    }

    /**
     *
     * @param object $product
     * @return mixed
     */
    public function setHardcodedAttributes($product)
    {
        foreach($this->hardCodedAttributes as $key => $hardCodedAttribute){
            $product->$key($hardCodedAttribute);
        }
        return $product;
    }

    /**
     * @param string $key i.e setCrossell
     * @param array $value Array of product skus
     * @param array $productData Array of product data from file
     * @return array of objects of linked products
     */
    public function setLinkedProducts($key, $value, $productData)
    {
        $linkDataAll = [];
        switch ($key) {
            case "setCrosssell":
                $type = "crosssell";
                break;
            case "setUpsell":
                $type = "upsell";
                break;
            default:
                $type = "related";
        }
        $skuLinks = explode(",",$value);
        foreach($skuLinks as $skuLink) {
            //check first that the product exist
            $linkedProduct = $this->productFactory->create()->loadByAttribute("sku",$skuLink);
            if($linkedProduct) {
                $linkData = $this->productLinks->create()
                    ->setSku($productData["setSku"])
                    ->setLinkedProductSku($skuLink)
                    ->setLinkType($type);
                $linkDataAll[] = $linkData;
            }
        }

        return $linkDataAll;
    }

    /**
     * @param object $product
     * @param array $productData
     */
    public function saveProduct($product, $productData)
    {
        // making sure that we can grab the errors in an array
        $product->setCollectExceptionMessages(true);
        $errors = $product->validate();

        //validate returns true if everything is ok and an array with the errors otherwise
        if (is_array($errors)) {
            $errors[] = " error on row ".$this->getRow();
            $this->setErrors(implode(",",$errors));
        } else {
            //proceed to save
            try{
                $product->save();
                $this->logger->info("Saved product sku ".$productData["setSku"]);
            } catch(Exception $e){
                $this->setErrors($e->getMessage());
                $this->logger->info($e->getMessage());
            }
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
