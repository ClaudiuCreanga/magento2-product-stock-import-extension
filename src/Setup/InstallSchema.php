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

namespace Limesharp\Import\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
	 */

	// @codingStandardsIgnoreStart
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)

		// @codingStandardsIgnoreEnd
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('limesharp_import_integration_products')) {
			$table = $installer->getConnection()
				->newTable($installer->getTable('limesharp_import_integration_products'));
			$table->addColumn(
				'file_id',
				Table::TYPE_INTEGER,
				null,
				[
					'identity' => true,
					'unsigned' => true,
					'nullable' => false,
					'primary' => true
				],
				'File ID'
			)
				->addColumn(
					'store_id',
					Table::TYPE_SMALLINT,
					null,
					[
						'unsigned' => true,
						'nullable' => false
					],
					'Store ID'
				)
				->addColumn(
					'name',
					Table::TYPE_TEXT,
					255,
					['nullable' => false,],
					'File Name'
				)
				->addColumn(
					'logs',
					Table::TYPE_TEXT,
					null,
					['nullable' => false,],
					'File Logs'
				)
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					null,
					[
						'nullable' => false,
						'default' => '1',
					],
					'Status'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					[],
					'Creation Time'
				)
				->setComment('List of product files');

			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('limesharp_import_integration_products'),
				$setup->getIdxName(
					$installer->getTable('limesharp_import_integration_products'),
					['name'],
					AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				[
					'name',
					'logs'
				],
				AdapterInterface::INDEX_TYPE_FULLTEXT
			);

			if (!$installer->tableExists('limesharp_import_integration_stock')) {
				$table = $installer->getConnection()
					->newTable($installer->getTable('limesharp_import_integration_stock'));
				$table->addColumn(
					'file_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true
					],
					'File ID'
				)
					->addColumn(
						'store_id',
						Table::TYPE_SMALLINT,
						null,
						[
							'unsigned' => true,
							'nullable' => false
						],
						'Store ID'
					)
					->addColumn(
						'name',
						Table::TYPE_TEXT,
						255,
						['nullable' => false,],
						'File Name'
					)
					->addColumn(
						'logs',
						Table::TYPE_TEXT,
						null,
						['nullable' => false,],
						'File Logs'
					)
					->addColumn(
						'status',
						Table::TYPE_INTEGER,
						null,
						[
							'nullable' => false,
							'default' => '1',
						],
						'Status'
					)
					->addColumn(
						'created_at',
						Table::TYPE_TIMESTAMP,
						null,
						[],
						'Creation Time'
					)
					->setComment('List of stock files');

				$installer->getConnection()->createTable($table);

				$installer->getConnection()->addIndex(
					$installer->getTable('limesharp_import_integration_stock'),
					$setup->getIdxName(
						$installer->getTable('limesharp_import_integration_stock'),
						['name'],
						AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					[
						'name',
						'logs'
					],
					AdapterInterface::INDEX_TYPE_FULLTEXT
				);

				$installer->endSetup();

			}
		}
	}
}
