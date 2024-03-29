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
 * @see Zend_Paginator_Adapter_Iterator
 */
require_once 'Zend/Paginator/Adapter/Iterator.php';

/**
 * @see \PHPUnit\Framework\TestCase
 */

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Paginator
 */
#[AllowDynamicProperties]
class Zend_Paginator_Adapter_IteratorTest extends \PHPUnit\Framework\TestCase
{
    private ?\Zend_Paginator_Adapter_Iterator $_adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $iterator = new ArrayIterator(range(1, 101));
        $this->_adapter = new Zend_Paginator_Adapter_Iterator($iterator);
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
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertTrue($actual instanceof LimitIterator);

        $i = 1;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            ++$i;
        }
    }

    public function testGetsItemsAtOffsetTen()
    {
        $actual = $this->_adapter->getItems(10, 10);
        $this->assertTrue($actual instanceof LimitIterator);

        $i = 11;
        foreach ($actual as $item) {
            $this->assertEquals($i, $item);
            ++$i;
        }
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->_adapter->count());
    }

    public function testThrowsExceptionIfNotCountable()
    {
        $iterator = new LimitIterator(new ArrayIterator(range(1, 101)));

        try {
            new Zend_Paginator_Adapter_Iterator($iterator);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Paginator_Exception);
            $this->assertEquals('Iterator must implement Countable', $e->getMessage());
        }
    }

    /**
     * @group ZF-4151
     */
    public function testDoesNotThrowOutOfBoundsExceptionIfIteratorIsEmpty()
    {
        $this->_paginator = Zend_Paginator::factory(new ArrayIterator([]));
        $items = $this->_paginator->getCurrentItems();

        try {
            foreach ($items as $item);
        } catch (OutOfBoundsException $e) {
            static::fail('Empty iterator caused in an OutOfBoundsException');
        }
        self::assertTrue(true);
    }

    /**
     * @group ZF-8084
     */
    public function testGetItemsSerializable()
    {
        $items = $this->_adapter->getItems(0, 1);
        $innerIterator = $items->getInnerIterator();
        $items = unserialize(serialize($items));
        $this->assertTrue(($items->getInnerIterator() == $innerIterator), 'getItems has to be serializable to use caching');
    }

    /**
     * @group ZF-4151
     */
    public function testEmptySet()
    {
        $iterator = new ArrayIterator([]);
        $this->_adapter = new Zend_Paginator_Adapter_Iterator($iterator);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals([], $actual);
    }
}
