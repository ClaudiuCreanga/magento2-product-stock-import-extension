<?php
declare(strict_types=1);
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
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
namespace Claudiucreanga\Import\Controller\Adminhtml\Products;

use Claudiucreanga\Import\Controller\Adminhtml\AbstractImportIntegration;

class Import extends AbstractImportIntegration
{
    /**
     * Import import.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Claudiucreanga_Import::import');
        $resultPage->getConfig()->getImport()->prepend(__('Import'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'), $this->getUrl('importintegration/import'));

        return $resultPage;
    }
}
