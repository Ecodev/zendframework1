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
 * @see Zend_Filter_Int
 */
require_once 'Zend/Filter/Null.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Filter
 */
#[AllowDynamicProperties]
class Zend_Filter_NullTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Filter_Null object.
     *
     * @var Zend_Filter_Null
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Null object for each test method.
     */
    public function setUp(): void
    {
        $this->_filter = new Zend_Filter_Null();
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testBasic()
    {
        $this->assertEquals(null, $this->_filter->filter('0'));
        $this->assertEquals(null, $this->_filter->filter(''));
        $this->assertEquals(null, $this->_filter->filter(0));
        $this->assertEquals(null, $this->_filter->filter([]));
        $this->assertEquals(null, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyBoolean()
    {
        $this->_filter->setType(Zend_Filter_Null::BOOLEAN);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals([], $this->_filter->filter([]));
        $this->assertEquals(null, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyInteger()
    {
        $this->_filter->setType(Zend_Filter_Null::INTEGER);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(null, $this->_filter->filter(0));
        $this->assertEquals([], $this->_filter->filter([]));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyArray()
    {
        $this->_filter->setType(Zend_Filter_Null::EMPTY_ARRAY);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals(null, $this->_filter->filter([]));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyString()
    {
        $this->_filter->setType(Zend_Filter_Null::STRING);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals(null, $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals([], $this->_filter->filter([]));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyZero()
    {
        $this->_filter->setType(Zend_Filter_Null::ZERO);
        $this->assertEquals(null, $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals([], $this->_filter->filter([]));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testArrayConstantNotation()
    {
        $filter = new Zend_Filter_Null(
            [
                Zend_Filter_Null::ZERO,
                Zend_Filter_Null::STRING,
                Zend_Filter_Null::BOOLEAN,
            ]
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals([], $filter->filter([]));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testArrayConfigNotation()
    {
        $filter = new Zend_Filter_Null(
            [
                'type' => [
                    Zend_Filter_Null::ZERO,
                    Zend_Filter_Null::STRING,
                    Zend_Filter_Null::BOOLEAN, ],
                'test' => false,
            ]
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals([], $filter->filter([]));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testMultiConstantNotation()
    {
        $filter = new Zend_Filter_Null(
            Zend_Filter_Null::ZERO + Zend_Filter_Null::STRING + Zend_Filter_Null::BOOLEAN
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals([], $filter->filter([]));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testStringNotation()
    {
        $filter = new Zend_Filter_Null(
            [
                'zero', 'string', 'boolean',
            ]
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals([], $filter->filter([]));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSingleStringNotation()
    {
        $filter = new Zend_Filter_Null(
            'boolean'
        );

        $this->assertEquals('0', $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals([], $filter->filter([]));
        $this->assertEquals(false, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingFalseType()
    {
        try {
            $this->_filter->setType(true);
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Unknown', $e->getMessage());
        }
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testGetType()
    {
        $this->assertEquals(31, $this->_filter->getType());
    }
}
