<?php

declare(strict_types=1);
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
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */

namespace Limesharp\Import\Ui\DataProvider\Import\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Limesharp\Import\Model\ResourceModel\Products\CollectionFactory as FileFactory;

class ImportData implements ModifierInterface
{
    /**
     * @var \Limesharp\Import\Model\ResourceModel\Products\Collection
     */
    public $collection;

    /**
     * @param CollectionFactory $productsFactory
     */
    public function __construct(
        FileFactory $productsFactory
    ) {
        $this->collection = $productsFactory->create();
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();

        return $data;
    }
}
