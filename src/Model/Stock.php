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

namespace Limesharp\Import\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Limesharp\Import\Model\ResourceModel\Stock as ModelStock;
use Limesharp\Import\Model\Source\AbstractSource;

class Stock extends AbstractModel
{

	/**
	 * @var int
	 */
	const STATUS_ENABLED = 1;
	/**
	 * @var int
	 */
	const STATUS_DISABLED = 0;

	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'limesharp_import_integration_stock';

	/**
	 * filter model
	 *
	 * @var \Magento\Framework\Filter\FilterManager
	 */
	public $filter;

	/**
	 * @var AbstractSource[]
	 */
	public $optionProviders;
	/**
	 * @param Context $context
	 * @param Registry $registry
	 * @param FilterManager $filter
	 * @param array $optionProviders
	 * @param array $data
	 * @param AbstractResource|null $resource
	 * @param AbstractDb|null $resourceCollection
	 */
	public function __construct(
		Context $context,
		Registry $registry,
		FilterManager $filter,
		array $optionProviders = [],
		array $data = [],
		AbstractResource $resource = null,
		AbstractDb $resourceCollection = null
	) {
		$this->filter          = $filter;
		$this->optionProviders = $optionProviders;
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init(ModelStock::class);
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	/**
	 * set name
	 *
	 * @param $name
	 * @return string
	 */
	public function setName($name)
	{
		return $this->setData("name", $name);
	}

	/**
	 * Set status
	 *
	 * @param $status
	 * @return string
	 */
	public function setStatus($status)
	{
		return $this->setData("status", $status);
	}

	/**
	 * set created at
	 *
	 * @param $createdAt
	 * @return string
	 */
	public function setCreatedAt($createdAt)
	{
		return $this->setData("created_at", $createdAt);
	}
	/**
	 * set updated at
	 *
	 * @param $updatedAt
	 * @return string
	 */
	public function setUpdatedAt($updatedAt)
	{
		return $this->setData("updated_at", $updatedAt);
	}
	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getData("name");
	}

	/**
	 * Get status
	 *
	 * @return bool|int
	 */
	public function getStatus()
	{
		return $this->getData("status");
	}
	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->getData("created_at");
	}
	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->getData("updated_at");
	}

	/**
	 * @return array
	 */
	public function getStoreId()
	{
		return $this->getData("store_id");
	}

	/**
	 * @return bool
	 */
	public function status()
	{
		return (bool)$this->getStatus();
	}
	/**
	 * @param $attribute
	 * @return string
	 */
	public function getAttributeText($attribute)
	{
		if (!isset($this->optionProviders[$attribute])) {
			return '';
		}
		if (!($this->optionProviders[$attribute] instanceof AbstractSource)) {
			return '';
		}
		return $this->optionProviders[$attribute]->getOptionText($this->getData($attribute));
	}

}
