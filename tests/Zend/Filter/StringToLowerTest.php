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
 * @see Zend_Filter_StringToLower
 */
require_once 'Zend/Filter/StringToLower.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Filter
 */
#[AllowDynamicProperties]
class Zend_Filter_StringToLowerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Filter_StringToLower object.
     *
     * @var Zend_Filter_StringToLower
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_StringToLower object for each test method.
     */
    public function setUp(): void
    {
        $this->_filter = new Zend_Filter_StringToLower();
    }

    /**
     * Ensures that the filter follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            'string' => 'string',
            'aBc1@3' => 'abc1@3',
            'A b C' => 'a b c',
        ];

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }

    /**
     * Ensures that the filter follows expected behavior with
     * specified encoding.
     */
    public function testWithEncoding()
    {
        $valuesExpected = [
            'Ü' => 'ü',
            'Ñ' => 'ñ',
            'ÜÑ123' => 'üñ123',
        ];

        try {
            $this->_filter->setEncoding('UTF-8');
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $this->_filter->filter($input));
            }
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('mbstring is required', $e->getMessage());
        }
    }

    public function testFalseEncoding()
    {
        if (!function_exists('mb_strtolower')) {
            $this->markTestSkipped('mbstring required');
        }

        try {
            $this->_filter->setEncoding('aaaaa');
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('is not supported', $e->getMessage());
        }
    }

    /**
     * @ZF-8989
     */
    public function testInitiationWithEncoding()
    {
        $valuesExpected = [
            'Ü' => 'ü',
            'Ñ' => 'ñ',
            'ÜÑ123' => 'üñ123',
        ];

        try {
            $filter = new Zend_Filter_StringToLower(['encoding' => 'UTF-8']);
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $filter->filter($input));
            }
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('mbstring is required', $e->getMessage());
        }
    }

    /**
     * @ZF-9058
     */
    public function testCaseInsensitiveEncoding()
    {
        $valuesExpected = [
            'Ü' => 'ü',
            'Ñ' => 'ñ',
            'ÜÑ123' => 'üñ123',
        ];

        try {
            $this->_filter->setEncoding('UTF-8');
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $this->_filter->filter($input));
            }

            $this->_filter->setEncoding('utf-8');
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $this->_filter->filter($input));
            }

            $this->_filter->setEncoding('UtF-8');
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $this->_filter->filter($input));
            }
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('mbstring is required', $e->getMessage());
        }
    }

    /**
     * @group ZF-9854
     */
    public function testDetectMbInternalEncoding()
    {
        if (!function_exists('mb_internal_encoding')) {
            $this->markTestSkipped("Function 'mb_internal_encoding' not available");
        }

        $this->assertEquals(mb_internal_encoding(), $this->_filter->getEncoding());
    }
}
