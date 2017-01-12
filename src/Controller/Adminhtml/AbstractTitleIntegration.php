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

namespace Limesharp\Import\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Limesharp\Import\Cron\Paths;
use Limesharp\Import\Cron\Products\Products;

/**
 * Class AbstractImportIntegration
 * @package Limesharp\Import\Controller\Adminhtml
 */
abstract class AbstractImportIntegration extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var Paths
     */
    public $paths;

    /**
     * @var Products
     */
    public $cronProducts;

    /**
     * AbstractImportIntegration constructor.
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param Paths $paths
     * @param Products $products
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        Paths $paths,
        Products $products,
        Context $context
    ) {
        $this->coreRegistry      = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->paths = $paths;
        $this->cronProducts = $products;
        parent::__construct($context);
    }

    /**
     * @param string $file
     */
    public function runASingleFile($file)
    {
        $this->cronProducts->setFiles([$this->paths->getIntegrationDirectory()."/".$file]);
        $this->cronProducts->runFiles();
    }
    /**
     * @param string $file
     */
    public function runManualImport($file)
    {
        $this->cronProducts->setFiles([$file]);
        $this->cronProducts->runFiles(False);
    }

}
