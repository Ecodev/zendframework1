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
 * @see Zend_Filter_Boolean
 */
require_once 'Zend/Filter/Boolean.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Filter
 */
#[AllowDynamicProperties]
class Zend_Filter_BooleanTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Filter_Boolean object.
     *
     * @var Zend_Filter_Boolean
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Boolean object for each test method.
     */
    public function setUp(): void
    {
        $this->_filter = new Zend_Filter_Boolean();
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testBasic()
    {
        $this->assertFalse($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertFalse($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertFalse($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertFalse($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertFalse($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertFalse($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertFalse($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyBoolean()
    {
        $this->_filter->setType(Zend_Filter_Boolean::BOOLEAN);
        $this->assertFalse($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyInteger()
    {
        $this->_filter->setType(Zend_Filter_Boolean::INTEGER);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertFalse($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyFloat()
    {
        $this->_filter->setType(Zend_Filter_Boolean::FLOAT);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertFalse($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyString()
    {
        $this->_filter->setType(Zend_Filter_Boolean::STRING);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertFalse($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyZero()
    {
        $this->_filter->setType(Zend_Filter_Boolean::ZERO);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertFalse($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyArray()
    {
        $this->_filter->setType(Zend_Filter_Boolean::EMPTY_ARRAY);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertFalse($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyNull()
    {
        $this->_filter->setType(Zend_Filter_Boolean::NULL);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertFalse($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyPHP()
    {
        $this->_filter->setType(Zend_Filter_Boolean::PHP);
        $this->assertFalse($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertFalse($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertFalse($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertFalse($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertFalse($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertFalse($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertFalse($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyFalseString()
    {
        $this->_filter->setType(Zend_Filter_Boolean::FALSE_STRING);
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertFalse($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyYes()
    {
        $this->_filter->setType(Zend_Filter_Boolean::YES);
        $this->_filter->setLocale('en');
        $this->assertTrue($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertTrue($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertTrue($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertTrue($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertTrue($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertTrue($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertTrue($this->_filter->filter(null));
        $this->assertTrue($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertFalse($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testOnlyAll()
    {
        $this->_filter->setType(Zend_Filter_Boolean::ALL);
        $this->_filter->setLocale('en');
        $this->assertFalse($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertFalse($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertFalse($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertFalse($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertFalse($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertFalse($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertFalse($this->_filter->filter(null));
        $this->assertFalse($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertFalse($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testArrayConstantNotation()
    {
        $filter = new Zend_Filter_Boolean(
            [
                'type' => [
                    Zend_Filter_Boolean::ZERO,
                    Zend_Filter_Boolean::STRING,
                    Zend_Filter_Boolean::BOOLEAN,
                ],
            ]
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertTrue($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertTrue($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertTrue($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertTrue($filter->filter(null));
        $this->assertTrue($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testArrayConfigNotation()
    {
        $filter = new Zend_Filter_Boolean(
            [
                'type' => [
                    Zend_Filter_Boolean::ZERO,
                    Zend_Filter_Boolean::STRING,
                    Zend_Filter_Boolean::BOOLEAN, ],
                'test' => false,
            ]
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertTrue($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertTrue($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertTrue($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertTrue($filter->filter(null));
        $this->assertTrue($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testMultiConstantNotation()
    {
        $filter = new Zend_Filter_Boolean(
            Zend_Filter_Boolean::ZERO + Zend_Filter_Boolean::STRING + Zend_Filter_Boolean::BOOLEAN
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertTrue($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertTrue($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertTrue($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertTrue($filter->filter(null));
        $this->assertTrue($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testStringNotation()
    {
        $filter = new Zend_Filter_Boolean(
            [
                'type' => ['zero', 'string', 'boolean'],
            ]
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertTrue($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertTrue($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertTrue($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertTrue($filter->filter(null));
        $this->assertTrue($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSingleStringNotation()
    {
        $filter = new Zend_Filter_Boolean(
            'boolean'
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertTrue($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertTrue($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertTrue($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertTrue($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertTrue($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertTrue($filter->filter(null));
        $this->assertTrue($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingLocale()
    {
        $this->_filter->setType(Zend_Filter_Boolean::ALL);
        $this->_filter->setLocale('de');
        $this->assertFalse($this->_filter->filter(false));
        $this->assertTrue($this->_filter->filter(true));
        $this->assertFalse($this->_filter->filter(0));
        $this->assertTrue($this->_filter->filter(1));
        $this->assertFalse($this->_filter->filter(0.0));
        $this->assertTrue($this->_filter->filter(1.0));
        $this->assertFalse($this->_filter->filter(''));
        $this->assertTrue($this->_filter->filter('abc'));
        $this->assertFalse($this->_filter->filter('0'));
        $this->assertTrue($this->_filter->filter('1'));
        $this->assertFalse($this->_filter->filter([]));
        $this->assertTrue($this->_filter->filter(['xxx']));
        $this->assertFalse($this->_filter->filter(null));
        $this->assertFalse($this->_filter->filter('false'));
        $this->assertTrue($this->_filter->filter('true'));
        $this->assertTrue($this->_filter->filter('no'));
        $this->assertTrue($this->_filter->filter('yes'));
        $this->assertFalse($this->_filter->filter('nein'));
        $this->assertTrue($this->_filter->filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingLocalePerConstructorString()
    {
        $filter = new Zend_Filter_Boolean(
            'all', true, 'de'
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertFalse($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertFalse($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertFalse($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertFalse($filter->filter(null));
        $this->assertFalse($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
        $this->assertFalse($filter->filter('nein'));
        $this->assertTrue($filter->filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testConfigObject()
    {
        $options = ['type' => 'all', 'locale' => 'de'];
        $config = new Zend_Config($options);

        $filter = new Zend_Filter_Boolean(
            $config
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertFalse($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertFalse($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertFalse($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertFalse($filter->filter(null));
        $this->assertFalse($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
        $this->assertFalse($filter->filter('nein'));
        $this->assertTrue($filter->filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingLocalePerConstructorArray()
    {
        $filter = new Zend_Filter_Boolean(
            ['type' => 'all', 'locale' => 'de']
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertFalse($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertFalse($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertFalse($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertFalse($filter->filter(null));
        $this->assertFalse($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
        $this->assertFalse($filter->filter('nein'));
        $this->assertTrue($filter->filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingLocaleInstance()
    {
        $locale = new Zend_Locale('de');
        $filter = new Zend_Filter_Boolean(
            ['type' => 'all', 'locale' => $locale]
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertFalse($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertFalse($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertFalse($filter->filter(''));
        $this->assertTrue($filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertFalse($filter->filter([]));
        $this->assertTrue($filter->filter(['xxx']));
        $this->assertFalse($filter->filter(null));
        $this->assertFalse($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertTrue($filter->filter('no'));
        $this->assertTrue($filter->filter('yes'));
        $this->assertFalse($filter->filter('nein'));
        $this->assertTrue($filter->filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testWithoutCasting()
    {
        $locale = new Zend_Locale('de');
        $filter = new Zend_Filter_Boolean(
            ['type' => 'all', 'casting' => false, 'locale' => $locale]
        );

        $this->assertFalse($filter->filter(false));
        $this->assertTrue($filter->filter(true));
        $this->assertFalse($filter->filter(0));
        $this->assertTrue($filter->filter(1));
        $this->assertEquals(2, $filter->filter(2));
        $this->assertFalse($filter->filter(0.0));
        $this->assertTrue($filter->filter(1.0));
        $this->assertEquals(0.5, $filter->filter(0.5));
        $this->assertFalse($filter->filter(''));
        $this->assertEquals('abc', $filter->filter('abc'));
        $this->assertFalse($filter->filter('0'));
        $this->assertTrue($filter->filter('1'));
        $this->assertEquals('2', $filter->filter('2'));
        $this->assertFalse($filter->filter([]));
        $this->assertEquals(['xxx'], $filter->filter(['xxx']));
        $this->assertEquals(null, $filter->filter(null));
        $this->assertFalse($filter->filter('false'));
        $this->assertTrue($filter->filter('true'));
        $this->assertEquals('no', $filter->filter('no'));
        $this->assertEquals('yes', $filter->filter('yes'));
        $this->assertFalse($filter->filter('nein'));
        $this->assertTrue($filter->filter('ja'));
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
        $this->assertEquals(127, $this->_filter->getType());
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingFalseLocaleType()
    {
        try {
            $this->_filter->setLocale(true);
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Locale has to be', $e->getMessage());
        }
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testSettingUnknownLocale()
    {
        try {
            $this->_filter->setLocale('yy');
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Unknown locale', $e->getMessage());
        }
    }
}
