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

namespace Claudiucreanga\Import\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class RerunActions extends Column
{

	/**
	 * Url path  to delete
	 *
	 * @var string
	 */
	const URL_PATH_RERUN = 'importintegration/products/rerun';
	/**
	 * URL builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	public $_urlBuilder;
	/**
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param UrlInterface $urlBuilder
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	) {
		$this->_urlBuilder = $urlBuilder;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as & $item) {
				if (isset($item['name'])) {
					$item[$this->getData('name')] = [
						'Rerun' => [
							'href' => $this->_urlBuilder->getUrl(
								static::URL_PATH_RERUN,
								[
									'file_name' => $item['name']
								]
							),
							'label' => __('Rerun'),
							'confirm' => [
								'import' => __('Rerun "${ $.$data.name }"'),
								'message' => __('Are you sure you wan\'t to rerun the file "${ $.$data.name }" ?')
							]
						]
					];
				}
			}
		}
		return $dataSource;
	}
}
