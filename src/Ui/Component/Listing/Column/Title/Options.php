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
namespace Claudiucreanga\Import\Ui\Component\Listing\Column\Import;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class Options extends StoreOptions
{
	/**
	 * All Store Views value
	 */
	const ALL_STORE_VIEWS = '0';

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		if ($this->options !== null) {
			return $this->options;
		}
		$this->currentOptions['All Store Views']['label'] = __('All Store Views');
		$this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;
		$this->generateCurrentOptions();
		$this->options = array_values($this->currentOptions);
		return $this->options;
	}
}
