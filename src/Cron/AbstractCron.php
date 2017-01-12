<?php
/**
 * Claudiucreanga_Import extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Claudiucreanga
 * @package   Claudiucreanga_Import
 * @copyright 2016 Claudiucreanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */

namespace Claudiucreanga\Import\Cron;

use Braintree\Exception;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\Block\Template\Context;
use Claudiucreanga\Import\Logger\Logger;
use Magento\Config\Model\ResourceModel\Config;

/**
 * Class AbstractCron
 * @package Claudiucreanga\Import\Cron
 */
abstract class AbstractCron
{
    /**
     *
     */
    const STATUS_CONFIG_PATH = "claudiucreanga_import/import_general/status";
    /**
     *
     */
    const EMAIL_ALERTS_CONFIG_PATH = "claudiucreanga_import/import_general/email_alerts";
    /**
     *
     */
    const EMAIL_ADDRESSES_CONFIG_PATH = "claudiucreanga_import/import_general/email_addresses";
    /**
     *
     */
    const PRODUCTS_TIMEOUT_CONFIG_PATH = "claudiucreanga_import/import_products/timeout";
    /**
     *
     */
    const PRODUCTS_LAST_TIME_CONFIG_PATH = "claudiucreanga_import/import_products/last_time";
    /**
     *
     */
    const PRODUCTS_EMAIL_TIME_CONFIG_PATH = "claudiucreanga_import/import_products/email_time";
    /**
     *
     */
    const STOCK_TIMEOUT_CONFIG_PATH = "claudiucreanga_import/import_stock/timeout";
    /**
     *
     */
    const STOCK_LAST_TIME_CONFIG_PATH = "claudiucreanga_import/import_stock/last_time";
    /**
     *
     */
    const STOCK_EMAIL_TIME_CONFIG_PATH = "claudiucreanga_import/import_stock/email_time";
    /**
     *
     */
    const STORE_EMAIL_CONFIG_PATH = "trans_email/ident_general/email";

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Logger
     */
    public $logger;
    /**
     * @var Config
     */
    public $resourceConfig;

    /**
     * @var array
     */
    public static $errors = [];

    /**
     * AbstractCron constructor.
     * @param Context $context
     * @param Logger $logger
     * @param Config $resourceConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Config $resourceConfig,
        array $data = []
    )
    {
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $context->getScopeConfig();
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getIntegrationStatusSettings()
    {
        return $this->scopeConfig->getValue(self::STATUS_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getEmailAlertsSettings()
    {
        return $this->scopeConfig->getValue(self::EMAIL_ALERTS_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getEmailAddressesSettings()
    {
        return $this->scopeConfig->getValue(self::EMAIL_ADDRESSES_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getStoreEmailAddress()
    {
        return $this->scopeConfig->getValue(self::STORE_EMAIL_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductsTimeoutSettings()
    {
        return $this->scopeConfig->getValue(self::PRODUCTS_TIMEOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getStockTimeoutSettings()
    {
        return $this->scopeConfig->getValue(self::STOCK_TIMEOUT_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductsLastTimeFileHasRun()
    {
        return $this->scopeConfig->getValue(self::PRODUCTS_LAST_TIME_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getStockLastTimeFileHasRun()
    {
        return $this->scopeConfig->getValue(self::STOCK_LAST_TIME_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductsTimeBeforeSendingAlerts()
    {
        return $this->scopeConfig->getValue(self::PRODUCTS_EMAIL_TIME_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getStockTimeBeforeSendingAlerts()
    {
        return $this->scopeConfig->getValue(self::STOCK_EMAIL_TIME_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $time String timestamp
     */
    public function setStockLastTimeFileHasRun($time)
    {
        $this->resourceConfig->saveConfig(self::STOCK_LAST_TIME_CONFIG_PATH, $time, ScopeInterface::SCOPE_STORE, 0);
    }

    /**
     * @param $time String timestamp
     */
    public function setProductsLastTimeFileHasRun($time)
    {
        $this->resourceConfig->saveConfig(self::PRODUCTS_LAST_TIME_CONFIG_PATH, $time, ScopeInterface::SCOPE_STORE, 0);
    }

    /**
     * If the processing directory is not empty then check if it timedout, that means, check the timeout value in the admin, check the last time it has run and then move it back into main integration directory to be run again.
     * @param string $type "products" or "stock"
     * @param array $file Array of files in the processing directory
     * @return bool
     */
    public function processingDirectoryIsNotEmpty($type, $file)
    {
        if($file[0]) {
            $lastEditTimeForFirstFileInArray = filectime($file[0]);
            $differenceBetweenNowAndLastEdit = time() - $lastEditTimeForFirstFileInArray;
            $timeout = $type == "products" ? $this->getProductsTimeoutSettings() : $this->getStockTimeoutSettings();
            if ($differenceBetweenNowAndLastEdit > $timeout) {
                $newPathOutsideProcessing = str_replace("/" . $type . "_processing", "", $file[0]);
                rename($file[0], $newPathOutsideProcessing);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *
     * checks if the last file has run successfully and when. if it was longer than the value in the admin then send an email
     * @param none
     * @return void
     *
     */
    public function checkLastFileThatRunSuccessfully($type)
    {
        if($type == "products"){
            $timeWhenLastFileHasRun = $this->getProductsLastTimeFileHasRun();
            $timeBufferBeforeSendingAlerts = $this->getProductsTimeBeforeSendingAlerts();
        } else {
            $timeWhenLastFileHasRun = $this->getStockLastTimeFileHasRun();
            $timeBufferBeforeSendingAlerts = $this->getStockTimeBeforeSendingAlerts();
        }
        if((time() - $timeWhenLastFileHasRun) > $timeBufferBeforeSendingAlerts){
            $body = "Please check the $type file, it seems that no file has been run successfully since ".date("F j, Y, g:i a",$this->getProductsLastTimeFileHasRun()).". Either import integration did not send us a file or magento failed to run it correctly. What would help in this case is to run the cron manually in Import Integration section. Also check the import_integration/stock_processing folder and ensure that no file is trapped there.";
            //$this->sendEmail($body);
        }
    }

    /**
     * Sends an email to the addresses specified in the admin
     * @param $body String body of email message
     */
    public function sendEmail($body)
    {
        if($this->getEmailAddressesSettings()){
            $emails = explode(',', $this->getEmailAddressesSettings());
            $mail = new \Zend_Mail();
            $mail->setBodyHtml( $body );
            $mail->setFrom($this->getStoreEmailAddress());
            foreach($emails as $email){
                $mail->addTo($email, '');
            }
            $mail->setSubject('Import Integration Error');
            try{
                $mail->send();
                $this->logger->info("Email successfully sent to ".$this->getEmailAddressesSettings());
            } catch (Exception $e){
                $this->logger->info("Failed to send email to ".$this->getEmailAddressesSettings());
            }
        }
    }

    /**
     * Updates the database with the file info that has just run
     * @param string $file
     * @param array $errors
     */
    public function updateTableWithFileInformation($fileFactory,$file)
    {
        $fileFactory->setName(basename($file));
        $fileFactory->setCreatedAt(time());
        $fileFactory->setLogs($this->recursiveImplode($this->getErrors()));
        $this->saveFile($fileFactory,$file);
    }

    /**
     * @param Object $product
     * @param array $productData
     */
    public function saveFile($fileFactory, $file)
    {
        try{
            $fileFactory->save();
            $this->logger->info("Cannot save file ".$file);
        } catch(Exception $e){
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return self::$errors;
    }

    /**
     * @param array $errors
     * @param array $empty If we should reset it or not
     */
    public function setErrors($errors, $empty = False)
    {
        if ($empty) {
            self::$errors = array();
        } else {
            self::$errors[] = $errors;
        }
    }

    /**
     * Recursively implodes an array with optional key inclusion
     *
     * Example of $include_keys output: key, value, key, value, key, value
     *
     * @access  public
     * @param   array   $array         multi-dimensional array to recursively implode
     * @param   string  $glue          value that glues elements together
     * @param   bool    $include_keys  include keys before their values
     * @param   bool    $trim_all      trim ALL whitespace from string
     * @return  string  imploded array
     */
    public function recursiveImplode(array $array, $glue = ',', $include_keys = false, $trim_all = false)
    {
        $glued_string = '';
        // Recursively iterates array and adds key/value to glued string
        array_walk_recursive($array, function($value, $key) use ($glue, $include_keys, &$glued_string)
        {
            $include_keys and $glued_string .= $key.$glue;
            $glued_string .= $value.$glue;
        });
        // Removes last $glue from string
        strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));
        // Trim ALL whitespace
        $trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);
        return (string) $glued_string;
    }

    /**
     * Checks the delimeters of the file and say if it is good or wrong
     * @param string $file
     * @param int $checkLines
     * @return string
     */
    public function getFileDelimiter($file, $checkLines = 2){
        $file = new \SplFileObject($file);
        $delimiters = array(
            ',',
            '\t',
            ';',
            '|',
            ':'
        );
        $results = array();
        $i = 0;
        while ($file->valid() && $i <= $checkLines) {
            $line = $file->fgets();
            foreach ($delimiters as $delimiter) {
                $regExp = '/['.$delimiter.']/';
                $fields = preg_split($regExp, $line);
                if (count($fields) > 1) {
                    if (!empty($results[$delimiter])) {
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }
                }
            }
            $i++;
        }
        $results = array_keys($results, max($results));
        return $results[0];
    }

}
