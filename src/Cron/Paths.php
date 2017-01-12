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

namespace  Claudiucreanga\Import\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Paths
 * @package Claudiucreanga\Import\Cron
 */
class Paths
{
	/**
	 * @var string
	 */
	const INTEGRATION_DIRECTORY = "import_integration";

	/**
	 * @var string
	 */
	const PROCESSING_DIRECTORY_PRODUCTS = "products_processing";

	/**
	 * @var string
	 */
	const PROCESSED_DIRECTORY_PRODUCTS = "products_processed";

	/**
	 * @var string
	 */
	const PROCESSING_DIRECTORY_STOCK = "stock_processing";

	/**
	 * @var string
	 */
	const PROCESSED_DIRECTORY_STOCK = "stock_processed";

	/**
	 * Retrieve var path
	 *
	 * @return string
	 */
	protected $directory_list;

	/**
	 * Paths constructor.
	 * @param DirectoryList $directory_list
	 */
	public function __construct(
		DirectoryList $directory_list
	)
	{
		$this->directory_list = $directory_list;
	}

	/**
	 * Retrieve var path
	 *
	 * @return string
	 */
	public function getVarFolderPath()
	{
		return $this->directory_list->getPath('media');
	}

	/**
	 * Retrieve integration directory
	 *
	 * @return string
	 */
	public function getIntegrationDirectory()
	{
		return $this->getVarFolderPath()."/".self::INTEGRATION_DIRECTORY;
	}

	/**
	 * Get full products processing path
	 *
	 * @return string
	 */
	public function getProductsProcessingDirectory()
	{
		return $this->getVarFolderPath()."/".self::INTEGRATION_DIRECTORY."/".self::PROCESSING_DIRECTORY_PRODUCTS;
	}

	/**
	 * Get full products processed path
	 *
	 * @return string
	 */
	public function getProductsProcessedDirectory()
	{
		return $this->getVarFolderPath()."/".self::INTEGRATION_DIRECTORY."/".self::PROCESSED_DIRECTORY_PRODUCTS;
	}

	/**
	 * Get full stock processing path
	 *
	 * @return string
	 */
	public function getStockProcessingDirectory()
	{
		return $this->getVarFolderPath()."/".self::INTEGRATION_DIRECTORY."/".self::PROCESSING_DIRECTORY_STOCK;
	}

	/**
	 * Get full stock processed path
	 *
	 * @return string
	 */
	public function getStockProcessedDirectory()
	{
		return $this->getVarFolderPath()."/".self::INTEGRATION_DIRECTORY."/".self::PROCESSED_DIRECTORY_STOCK;
	}

	/**
	 * Check if the core directories exists, if not create them
	 *
	 * @return void
	 */
	public function createDirectoriesIfTheyDontExist()
	{
		$directories = array(
			$this->getIntegrationDirectory(),
			$this->getProductsProcessingDirectory(),
			$this->getProductsProcessedDirectory(),
			$this->getStockProcessingDirectory(),
			$this->getStockProcessedDirectory()
		);
		foreach ($directories as $directory){
			if(!file_exists($directory)){
				mkdir($directory);
			}
		}
	}
}
