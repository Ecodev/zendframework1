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

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_AbstractTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_AbstractTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Creates a new validation object for each test method.
     */
    public function setUp(): void
    {
        $this->validator = new Zend_Validate_AbstractTest_Concrete();
    }

    public function tearDown(): void
    {
        Zend_Validate_Abstract::setMessageLength(-1);
    }

    public function testObscureValueFlagFalseByDefault()
    {
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testCanSetObscureValueFlag()
    {
        $this->testObscureValueFlagFalseByDefault();
        $this->validator->setObscureValue(true);
        $this->assertTrue($this->validator->getObscureValue());
        $this->validator->setObscureValue(false);
        $this->assertFalse($this->validator->getObscureValue());
    }

    public function testValueIsObfuscatedWheObscureValueFlagIsTrue()
    {
        $this->validator->setObscureValue(true);
        $this->assertFalse($this->validator->isValid('foobar'));
        $messages = $this->validator->getMessages();
        $this->assertTrue(isset($messages['fooMessage']));
        $message = $messages['fooMessage'];
        $this->assertStringNotContainsString('foobar', $message);
        $this->assertStringContainsString('******', $message);
    }

    /**
     * @group ZF-4463
     */
    public function testDoesNotFailOnObjectInput()
    {
        $this->assertFalse($this->validator->isValid(new stdClass()));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('fooMessage', $messages));
    }

    public function testGetMessageTemplates()
    {
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals(
            ['fooMessage' => '%value% was passed'], $messages);

        $this->assertEquals(
            [
                Zend_Validate_AbstractTest_Concrete::FOO_MESSAGE => '%value% was passed', ], $messages);
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred.
     *
     * @param  int $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  int $errline
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline)
    {
        $this->_errorOccurred = true;
    }
}

#[AllowDynamicProperties]
class Zend_Validate_AbstractTest_Concrete extends Zend_Validate_Abstract
{
    public const FOO_MESSAGE = 'fooMessage';

    protected $_messageTemplates = [
        'fooMessage' => '%value% was passed',
    ];

    public function isValid($value)
    {
        $this->_setValue($value);
        $this->_error(self::FOO_MESSAGE);

        return false;
    }
}
