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
namespace Limesharp\Import\Controller\Adminhtml\Stock;

use Limesharp\Import\Controller\Adminhtml\AbstractImportIntegration;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Rerun extends AbstractImportIntegration
{
	/**
	 * Products list.
	 *
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		$fileName = $this->getRequest()->getParam('file_name');
		if ($fileName) {
			try {
				$this->runASingleFile($fileName);
				$this->messageManager->addSuccessMessage(__('The file has run.'));
				$resultRedirect->setPath('importintegration/products/');
				return $resultRedirect;
			} catch (NoSuchEntityException $e) {
				$this->messageManager->addErrorMessage(__('The file no longer exists on the system.'));
				return $resultRedirect->setPath('importintegration/products/');
			} catch (LocalizedException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
				return $resultRedirect->setPath('importintegration/products/');
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage(__('There was a problem running the file'));
				return $resultRedirect->setPath('importintegration/products/');
			}
		}
		$this->messageManager->addErrorMessage(__('We can\'t run your file.'));
		$resultRedirect->setPath('importintegration/products/');
		return $resultRedirect;
	}
}
