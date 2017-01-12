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

namespace Claudiucreanga\Import\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Magento\Framework\Event\ManagerInterface;

class Products extends AbstractDb
{
	/**
	 * Store model
	 *
	 * @var \Magento\Store\Model\Store
	 */
	public $store = null;
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	public $date;
	/**
	 * Store manager
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	public $storeManager;
	/**
	 * @var \Magento\Framework\Stdlib\DateTime
	 */
	public $dateTime;
	/**
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	public $eventManager;
	/**
	 * @param Context $context
	 * @param DateTime $date
	 * @param StoreManagerInterface $storeManager
	 * @param LibDateTime $dateTime
	 * @param ManagerInterface $eventManager
	 */
	public function __construct(
		Context $context,
		DateTime $date,
		StoreManagerInterface $storeManager,
		LibDateTime $dateTime,
		ManagerInterface $eventManager
	) {
		$this->date             = $date;
		$this->storeManager     = $storeManager;
		$this->dateTime         = $dateTime;
		$this->eventManager     = $eventManager;
		parent::__construct($context);
	}
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('claudiucreanga_import_integration_products', 'file_id');
	}
}
