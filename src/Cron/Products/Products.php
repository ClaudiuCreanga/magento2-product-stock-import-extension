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
declare(strict_types=1);

namespace Claudiucreanga\Import\Cron\Products;

use Claudiucreanga\Import\Cron\Paths;
use Claudiucreanga\Import\Cron\FieldsHeader;
use Claudiucreanga\Import\Cron\Products\ProcessProducts;
use Claudiucreanga\Import\Logger\Logger;
use Claudiucreanga\Import\Cron\AbstractCron;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\ResourceModel\Config;
use Claudiucreanga\Import\Model\ProductsFactory as FileFactory;

/**
 * Class Products
 * @package Claudiucreanga\Import\Cron
 */
class Products extends AbstractCron
{

    /**
     * @var Paths
     */
    public $paths;

    /**
     * @var FieldsHeader
     */
    public $fieldsHeader;

    /**
     * @var array $files Array of files to run
     */
    public $files;

    /**
     * @var array
     */
    public $processingFiles;

    /**
     * @var \Claudiucreanga\Import\Cron\Products\ProcessProducts
     */
    public $processProducts;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var CollectionFactory
     */
    public $fileFactory;

    /**
     * Products constructor.
     * @param Context $context
     * @param Logger $logger
     * @param Paths $paths
     * @param FieldsHeader $fieldsHeader
     * @param \Claudiucreanga\Import\Cron\Products\ProcessProducts $processProducts
     * @param Config $resourceConfig
     * @param FileFactory $fileFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Paths $paths,
        FieldsHeader $fieldsHeader,
        ProcessProducts $processProducts,
        Config $resourceConfig,
        FileFactory $fileFactory,
        array $data = []
    ) {
        $this->paths = $paths;
        $this->fieldsHeader = $fieldsHeader;
        $this->processProducts = $processProducts;
        $this->logger = $logger;
        $this->fileFactory = $fileFactory;
        parent::__construct($context, $logger, $resourceConfig, $data);
    }

    /**
     * Main method called by the cron
     * performs checks and then is processing all files
     * to actually process products it is using the ProcessProducts Class
     * @return void
     */
    public function execute()
    {
        // if module is not enabled, return here
        if (!$this->getIntegrationStatusSettings()) {
            return;
        }
        $this->paths->createDirectoriesIfTheyDontExist();
        $this->checkLastFileThatRunSuccessfully("products");
        $this->checkFilesToProcess();
        $this->checkProcessingDirectory();

        // if there are no files, stop here
        if (empty($this->getFiles())) {
            return;
        }

        if (!empty($this->getProcessingFiles())) {
            if ($this->processingDirectoryIsNotEmpty("products", $this->getProcessingFiles())) {
                $this->execute();
            } else {
                return;
            };
        }

        //if everything ok, just run the files
        $this->runFiles(True);
    }

    /**
     * Loop through files and run them
     * @param bool $rename
     * @return void
     */
    public function runFiles($rename)
    {

        foreach ($this->getFiles() as $file) {
            $fileFactory = $this->fileFactory->create();
            $this->logger->info('Start product import for file '.$file);
            $f = fopen($file, "r") or die("Cannot open file");

            // first check if the delimiter is correct
            if ($this->getFileDelimiter($file, 5) != '\t'){
                $this->setErrors('Your file is not properly delimited. Please check that it is delimited by a tab and fields are quoted. The file was not moved and and cannot be viewed. ');
                $this->updateTableWithFileInformation($fileFactory, $file);
                $this->setErrors([], True);
                continue;
            }

            // place it in the processing directory while the file is running to avoid
            // multiple files processing in the same time if one file takes longer
            // don't do it if $rename is false (when it is run manually via the admin)
            if ($rename) {
                rename($file, $this->paths->getProductsProcessingDirectory() . "/" . basename($file));
            }

            $row = 0;
            while ( ($data = fgetcsv( $f, 0, "\t") ) !== FALSE) {

                # skip if there aren't enough rows. Prevent against empty files.
                if (sizeof($data) < 2 ) {
                    continue;
                }

                #skip headers
                if ($row === 0) {
                    $row++;
                    continue;
                }
                if (count($this->fieldsHeader->getFieldsHeader()) != count($data)) {
                    $this->logger->info('Product file incorrectly formated (different number of columns) '.$file);
                    $this->setErrors(['Product file incorrectly formated (different number of columns) '.$file]);
                    $this->updateTableWithFileInformation($fileFactory, $file);
                    continue;
                }

                $productInformationWithheaders = array_combine($this->fieldsHeader->getFieldsHeader(), $data);
                $this->processProducts->manageTypeOfProducts($productInformationWithheaders, $row);

                $row++;
            }

            // place it in the processed directory afterwards
            if ($rename) {
                rename(
                    $this->paths->getProductsProcessingDirectory() . "/" . basename($file),
                    $this->paths->getProductsProcessedDirectory() . "/" . basename($file)
                );
            } else {
                $this->setErrors('File uploaded manually, cannot be rerun or viewed. ');
            }
            $this->updateTableWithFileInformation($fileFactory, $file);
            $this->setErrors([], True);
            $this->logger->info('End product import for file '.$file);
        }
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getProcessingFiles()
    {
        return $this->processingFiles;
    }

    /**
     * @param mixed $processingFiles
     */
    public function setProcessingFiles($processingFiles)
    {
        $this->processingFiles = $processingFiles;
    }

    /**
     * Checks if there are files in the integration directory and sets in in the $files property
     * @return void
     */
    public function checkFilesToProcess()
    {
        $allFilesInIntegrationFolder = glob( $this->paths->getIntegrationDirectory().'/PRODUCT*.txt');
        $this->setFiles($allFilesInIntegrationFolder);
    }

    /**
     * Checks if there are files in the processing directory and sets them in the $processingFiles property
     * @return void
     */
    public function checkProcessingDirectory()
    {
        $allFilesInProcessingFolder = glob( $this->paths->getProductsProcessingDirectory().'/*.txt');
        $this->setProcessingFiles($allFilesInProcessingFolder);
    }

}
