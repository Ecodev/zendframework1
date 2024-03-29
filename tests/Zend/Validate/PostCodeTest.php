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
 * @version    $Id: PostCodeTest.php 17798 2009-08-24 20:07:53Z thomas $
 */

/**
 * @see Zend_Validate_PostCode
 */
require_once 'Zend/Validate/PostCode.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_PostCodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Validate_PostCode object.
     *
     * @var Zend_Validate_PostCode
     */
    protected $_validator;

    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_PostCodeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Validate_PostCode object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_PostCode('de_AT');
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            ['2292', true],
            ['1000', true],
            ['0000', true],
            ['12345', false],
            [1234, true],
            [9821, true],
            ['21A4', false],
            ['ABCD', false],
            [true, false],
            ['AT-2292', false],
            [1.56, false],
        ];

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value.
     */
    public function testGetMessages()
    {
        $this->assertEquals([], $this->_validator->getMessages());
    }

    /**
     * Ensures that a region is available.
     */
    public function testSettingLocalesWithoutRegion()
    {
        try {
            $this->_validator->setLocale('de');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Unable to detect a region', $e->getMessage());
        }
    }

    /**
     * Ensures that the region contains postal codes.
     */
    public function testSettingLocalesWithoutPostalCodes()
    {
        try {
            $this->_validator->setLocale('nus_SD');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Unable to detect a postcode format', $e->getMessage());
        }
    }

    /**
     * Ensures locales can be retrieved.
     */
    public function testGettingLocale()
    {
        $this->assertEquals('de_AT', $this->_validator->getLocale());
    }

    /**
     * Ensures format can be set and retrieved.
     */
    public function testSetGetFormat()
    {
        $this->_validator->setFormat('\d{1}');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('/^\d{1}');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('/^\d{1}$/');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        $this->_validator->setFormat('\d{1}$/');
        $this->assertEquals('/^\d{1}$/', $this->_validator->getFormat());

        try {
            $this->_validator->setFormat(null);
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('A postcode-format string has to be given', $e->getMessage());
        }

        try {
            $this->_validator->setFormat('');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('A postcode-format string has to be given', $e->getMessage());
        }
    }

    /**
     * @group ZF-9212
     */
    public function testErrorMessageText()
    {
        $this->assertFalse($this->_validator->isValid('hello'));
        $message = $this->_validator->getMessages();
        $this->assertStringContainsString('not appear to be a postal code', $message['postcodeNoMatch']);
    }
}
