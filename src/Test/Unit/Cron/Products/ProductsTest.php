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

namespace Limesharp\Import\Test\Unit\Model;


use Limesharp\Import\Cron\FieldsHeader;
use Limesharp\Import\Cron\Products\ProcessProducts;
use Limesharp\Import\Cron\Products\Products;
use Limesharp\Import\Cron\Paths;
use Limesharp\Import\Logger\Logger;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\ResourceModel\Config;
use Limesharp\Import\Model\ProductsFactory as FileFactory;

class ProductsTest extends \PHPUnit_Framework_TestCase
{

    const FILE_PATH = "/magento2/var/import_integration/PRODUCTS13-06-16-1624.txt";

    public $products;



    public function setUp(){

        /** @var \PHPUnit_Framework_MockObject_MockObject|Context $context */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Paths $paths */
        $pathsMock = $this->getMockBuilder(Paths::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pathsMock->expects($this->once())
            ->method("getIntegrationDirectory")
            ->willReturn("/magento2/var/import_integration");

        /** @var \PHPUnit_Framework_MockObject_MockObject|FieldsHeader $fieldsHeader */
        $fieldsHeaderMock = $this->getMockBuilder(FieldsHeader::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|ProcessProducts $processProducts */
        $processProductsMock = $this->getMockBuilder(ProcessProducts::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Logger $logger */
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Config $ressourceConfig */
        $ressourceConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|FileFactory $fileFactory */
        $fileFactoryMock = $this->getMockBuilder(FileFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->products = new Products(
            $contextMock,
            $loggerMock,
            $pathsMock,
            $fieldsHeaderMock,
            $processProductsMock,
            $ressourceConfigMock,
            $fileFactoryMock
        );
    }

    public function testCheckFilesToProcess()
    {
        $this->products->checkFilesToProcess();
        $this->assertContains("/magento2/var/import_integration/PRODUCTS_COMPLETE.txt", $this->products->getFiles());
    }


}
