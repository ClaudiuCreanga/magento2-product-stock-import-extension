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

namespace Limesharp\Import\Model\ResourceModel\Stock\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Limesharp\Import\Model\ResourceModel\Stock\Collection as StockCollection;

class Collection extends StockCollection implements SearchResultInterface
{
	/**
	 * Resource initialization
	 * @return $this
	 */
	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		$mainTable,
		$eventPrefix,
		$eventObject,
		$resourceModel,
		$model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
		$connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	)
	{
		parent::__construct(
			$entityFactory,
			$logger,
			$fetchStrategy,
			$eventManager,
			$storeManager,
			$connection,
			$resource
		);
		$this->_eventPrefix = $eventPrefix;
		$this->_eventObject = $eventObject;
		$this->_init($model, $resourceModel);
		$this->setMainTable($mainTable);
	}

	/**
	 * @return AggregationInterface
	 */
	public function getAggregations()
	{
		return $this->aggregations;
	}

	/**
	 * @param AggregationInterface $aggregations
	 *
	 * @return $this
	 */
	public function setAggregations($aggregations)
	{
		$this->aggregations = $aggregations;
	}


	/**
	 * Get search criteria.
	 *
	 * @return \Magento\Framework\Api\SearchCriteriaInterface|null
	 */
	public function getSearchCriteria()
	{
		return null;
	}

	/**
	 * Set search criteria.
	 *
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 *
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setSearchCriteria(
		\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
	)
	{
		return $this;
	}

	/**
	 * Get total count.
	 *
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->getSize();
	}

	/**
	 * Set total count.
	 *
	 * @param int $totalCount
	 *
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setTotalCount($totalCount)
	{
		return $this;
	}

	/**
	 * Set items list.
	 *
	 * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
	 *
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setItems(array $items = null)
	{
		return $this;
	}
}
