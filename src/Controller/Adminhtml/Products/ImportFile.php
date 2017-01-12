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
namespace Limesharp\Import\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Limesharp\Import\Controller\Adminhtml\AbstractImportIntegration;
use Limesharp\Import\Cron\Paths;
use Limesharp\Import\Cron\Products\Products;


/**
 * Class ImportFile
 * @package Limesharp\Import\Controller\Adminhtml\Products
 */
class ImportFile extends AbstractImportIntegration
{

    /**
     * ImportFile constructor.
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
        parent::__construct($registry, $resultPageFactory, $paths, $products, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $filePath = $data["import"][0]["path"].$data["import"][0]["file"];
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data["import"][0]["path"] && $data["import"][0]["file"]) {
            try {
                $this->runManualImport($filePath);
                rename($filePath,$this->paths->getProductsProcessedDirectory() . "/" . basename($filePath));
                $this->messageManager->addSuccessMessage(__('The file has run.'));
                $resultRedirect->setPath('importintegration/products/import/');
                $this->messageManager->addSuccessMessage(__('Please check importintegration -> products for errors during import. '));

            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The file no longer exists on the system.'));
                return $resultRedirect->setPath('importintegration/products/import/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('importintegration/products/import/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem running the file'));
                return $resultRedirect->setPath('importintegration/products/import/');
            }

        } else {
            $this->messageManager->addError(__('Please upload a file'));
        }

        return $resultRedirect;
    }
}
