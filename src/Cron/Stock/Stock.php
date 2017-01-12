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

namespace Claudiucreanga\Import\Cron\Stock;

use Claudiucreanga\Import\Cron\Paths;
use Claudiucreanga\Import\Cron\FieldsHeader;
use Claudiucreanga\Import\Cron\Stock\ProcessStockSave;
use Claudiucreanga\Import\Logger\Logger;
use Claudiucreanga\Import\Cron\AbstractCron;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\ResourceModel\Config;
use Claudiucreanga\Import\Model\StockFactory as FileFactory;

/**
 * Class Stock
 * @package Claudiucreanga\Import\Cron\Stock
 */
class Stock extends AbstractCron
{

    /**
     * @var Paths
     */
    public $paths;

    /**
     * @var array
     */
    public $files;

    /**
     * @var array
     */
    public $processingFiles;

    /**
     * @var \Claudiucreanga\Import\Cron\Stock\ProcessStockDatabase
     */
    public $processStockDatabase;
    /**
     * @var \Claudiucreanga\Import\Cron\Stock\ProcessStockSave
     */
    public $processStockSave;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var CollectionFactory
     */
    public $fileFactory;

    /**
     * Stock constructor.
     * @param Context $context
     * @param Logger $logger
     * @param Paths $paths
     * @param \Claudiucreanga\Import\Cron\Stock\ProcessStockSave $processStockSave
     * @param Config $resourceConfig
     * @param FileFactory $fileFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Paths $paths,
        ProcessStockSave $processStockSave,
        Config $resourceConfig,
        FileFactory $fileFactory,
        array $data = []
    ) {
        $this->paths = $paths;
        $this->processStockSave = $processStockSave;
        $this->logger = $logger;
        $this->fileFactory = $fileFactory;
        parent::__construct($context, $logger, $resourceConfig, $data);
    }

    /**
     * Main method called by the cron
     * performs checks and then is processing all files
     * to actually process stock it is using the ProcessStockSave
     * Class or ProcessStockDatabase Class depending on how big is
     * the file. If the file is big then write directly to the database
     */
    public function execute()
    {
        // if module is not enabled, return here
        if(!$this->getIntegrationStatusSettings()){
            return;
        }

        $this->paths->createDirectoriesIfTheyDontExist();
        $this->checkLastFileThatRunSuccessfully("stock");
        $this->checkFilesToProcess();
        $this->checkProcessingDirectory();

        // if there are no files, stop here
        if(empty($this->getFiles())) {
            return;
        }

        if(!empty($this->getProcessingFiles())){
            if($this->processingDirectoryIsNotEmpty("stock",$this->getProcessingFiles())){
                $this->execute();
            } else {
                return;
            };
        }

        //if everything ok, just run the files
        $this->runFiles();
    }

    /**
     * Loop through files and run them
     * @return void
     */
    public function runFiles()
    {
        foreach($this->getFiles() as $file){

            $fileFactory = $this->fileFactory->create();
            $this->logger->info('Start stock import for file '.$file);
            $f = fopen($file, "r") or die("Cannot open file");

            // first check if the delimiter is correct
            if ($this->getFileDelimiter($file, 5) != '\t'){
                $this->setErrors('Your file is not properly delimited. Please check that it is delimited by a tab. The file was not moved and and cannot be viewed. ');
                $this->updateTableWithFileInformation($fileFactory, $file);
                $this->setErrors([], True);
                continue;
            }

            //place it in the processing directory while the file is running to avoid multiple
            //files processing in the same time if one file takes longer
            rename($file, $this->paths->getStockProcessingDirectory()."/".basename($file));

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

                $this->processStockSave->updateStockOnSave($data, $row);

                $row++;
            }

            // place it in the processed directory afterwards
			rename(
				$this->paths->getStockProcessingDirectory()."/".basename($file),
				$this->paths->getStockProcessedDirectory()."/".basename($file)
			);

            $this->updateTableWithFileInformation($fileFactory,$file);
            $this->setErrors([], True);
            $this->logger->info('End stock import for file '.$file);
            $this->setStockLastTimeFileHasRun(time());
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
     * Checks if there are files in the integration directory and sets it in the $files property
     */
    public function checkFilesToProcess()
    {
        $allFilesInIntegrationFolder = glob( $this->paths->getIntegrationDirectory().'/STOCK*.txt');
        $this->setFiles($allFilesInIntegrationFolder);
    }

    /**
     * Checks if there are files in the processing directory and sets them in the $processingFiles property
     */
    public function checkProcessingDirectory()
    {
        $allFilesInProcessingFolder = glob($this->paths->getStockProcessingDirectory() . '/*.txt');
        $this->setProcessingFiles($allFilesInProcessingFolder);
    }

    /**
     * @param $file String path to the file
     * @return int
     */
    public function getNumberOfLinesInFile($file)
    {
        $linecount = 0;
        $handle = fopen($file, "r") or die("Cannot open file");
        while(!feof($handle)){
            $linecount += substr_count(fread($handle, 8192), "\n");
        }
        fclose($handle);
        return $linecount;
    }
}
