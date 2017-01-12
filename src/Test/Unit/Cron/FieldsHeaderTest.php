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

/**
 * Class FieldsHeaderTest
 * @package Limesharp\Import\Test\Unit\Model
 */
class FieldsHeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    private $fieldsHeader;

    /**
     * @param FieldsHeader $fieldsHeader
     */
    public function setUp()
    {
        $this->fieldsHeader = new FieldsHeader();
    }

    /**
     *
     * @test Limesharp\Import\Cron\FieldsHeader::getFieldsHeaderTest()
     */
    public function getFieldsHeaderTest()
    {
        $this->assertContains("setSku", $this->fieldsHeader->getFieldsHeader());
    }
}
