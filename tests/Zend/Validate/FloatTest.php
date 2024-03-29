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
 * @see Zend_Validate_Float
 */
require_once 'Zend/Validate/Float.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_FloatTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Validate_Float object.
     *
     * @var Zend_Validate_Float
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Float object for each test method.
     */
    public function setUp(): void
    {
        $this->_locale = setlocale(LC_ALL, 0); //backup locale

        if (Zend_Registry::isRegistered(\Zend_Locale::class)) {
            Zend_Registry::getInstance()->offsetUnset(\Zend_Locale::class);
        }

        $this->_validator = new Zend_Validate_Float();
    }

    public function tearDown(): void
    {
        //restore locale
        if (is_string($this->_locale) && strpos($this->_locale, ';')) {
            $locales = [];
            foreach (explode(';', $this->_locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);

            return;
        }
        setlocale(LC_ALL, $this->_locale);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            [1.00, true],
            [0.01, true],
            [-0.1, true],
            [1, true],
            ['not a float', false],
        ];
        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]));
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
     * Ensures that set/getLocale() works.
     */
    public function testSettingLocales()
    {
        $this->_validator->setLocale('de');
        $this->assertEquals('de', $this->_validator->getLocale());
        $this->assertEquals(true, $this->_validator->isValid('10,5'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid([1 => 1]));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale()
    {
        Zend_Registry::set(\Zend_Locale::class, new Zend_Locale('de'));
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid('123,456'));
    }

    /**
     * @ZF-7987
     */
    public function testNoZendLocaleButPhpLocale()
    {
        setlocale(LC_ALL, 'de');
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid(123));
        $this->assertTrue($valid->isValid('123,456'));
    }

    /**
     * @ZF-7987
     */
    public function testLocaleDeFloatType()
    {
        $this->_validator->setLocale('de');
        $this->assertEquals('de', $this->_validator->getLocale());
        $this->assertEquals(true, $this->_validator->isValid(10.5));
    }

    /**
     * @ZF-7987
     */
    public function testPhpLocaleDeFloatType()
    {
        setlocale(LC_ALL, 'de');
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid(10.5));
    }

    /**
     * @ZF-7987
     */
    public function testPhpLocaleFrFloatType()
    {
        setlocale(LC_ALL, 'fr');
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid(10.5));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleDeStringType()
    {
        setlocale(LC_ALL, 'de_AT');
        setlocale(LC_NUMERIC, 'de_AT');
        $valid = new Zend_Validate_Float('de_AT');
        $this->assertTrue($valid->isValid('1,3'));
        $this->assertTrue($valid->isValid('1000,3'));
        $this->assertTrue($valid->isValid('1.000,3'));
        $this->assertFalse($valid->isValid('1.3'));
        $this->assertFalse($valid->isValid('1000.3'));
        $this->assertFalse($valid->isValid('1,000.3'));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleFrStringType()
    {
        $valid = new Zend_Validate_Float('fr_FR');
        $this->assertTrue($valid->isValid('1,3'));
        $this->assertTrue($valid->isValid('1000,3'));
        $this->assertTrue($valid->isValid('1 000,3'));
        $this->assertFalse($valid->isValid('1.3'));
        $this->assertFalse($valid->isValid('1000.3'));
        $this->assertFalse($valid->isValid('1,000.3'));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleEnStringType()
    {
        $valid = new Zend_Validate_Float('en_US');
        $this->assertTrue($valid->isValid('1.3'));
        $this->assertTrue($valid->isValid('1000.3'));
        $this->assertTrue($valid->isValid('1,000.3'));
        $this->assertFalse($valid->isValid('1,3'));
        $this->assertFalse($valid->isValid('1000,3'));
        $this->assertFalse($valid->isValid('1.000,3'));
    }
}
