<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

/**
 * @see Zend_Paginator_Adapter_Array
 */
require_once 'Zend/Paginator/Adapter/Array.php';

/**
 * @see \PHPUnit\Framework\TestCase
 */

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Paginator
 */
#[AllowDynamicProperties]
class Zend_Paginator_Adapter_ArrayTest extends \PHPUnit\Framework\TestCase
{
    private ?\Zend_Paginator_Adapter_Array $_adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_adapter = new Zend_Paginator_Adapter_Array(range(1, 101));
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        $this->_adapter = null;
        parent::tearDown();
    }

    public function testGetsItemsAtOffsetZero()
    {
        $expected = range(1, 10);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals($expected, $actual);
    }

    public function testGetsItemsAtOffsetTen()
    {
        $expected = range(11, 20);
        $actual = $this->_adapter->getItems(10, 10);
        $this->assertEquals($expected, $actual);
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->_adapter->count());
    }

    /**
     * @group ZF-4151
     */
    public function testEmptySet()
    {
        $this->_adapter = new Zend_Paginator_Adapter_Array([]);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals([], $actual);
    }
}
