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

// Call Zend_Validate_MessageTest::main() if this source file is executed directly.

/**
 * @see Zend_Validate_StringLength
 */
require_once 'Zend/Validate/StringLength.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_MessageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Default instance created for all test methods.
     *
     * @var Zend_Validate_StringLength
     */
    protected $_validator;

    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite(self::class);
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Validate_StringLength object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_StringLength(4, 8);
    }

    /**
     * Ensures that we can change a specified message template by its key
     * and that this message is returned when the input is invalid.
     */
    public function testSetMessage()
    {
        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals("'$inputInvalid' is more than 8 characters long", current($messages));

        $this->_validator->setMessage(
            'Your value is too long',
            Zend_Validate_StringLength::TOO_LONG
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));
    }

    /**
     * Ensures that if we don't specify the message key, it uses
     * the first one in the list of message templates.
     * In the case of Zend_Validate_StringLength, TOO_SHORT is
     * the one we should expect to change.
     */
    public function testSetMessageDefaultKey()
    {
        $this->_validator->setMessage(
            'Your value is too short', Zend_Validate_StringLength::TOO_SHORT
        );

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too short', current($messages));
        $errors = $this->_validator->getErrors();
        $this->assertEquals(Zend_Validate_StringLength::TOO_SHORT, current($errors));
    }

    /**
     * Ensures that we can include the %value% parameter in the message,
     * and that it is substituted with the value we are validating.
     */
    public function testSetMessageWithValueParam()
    {
        $this->_validator->setMessage(
            "Your value '%value%' is too long",
            Zend_Validate_StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals("Your value '$inputInvalid' is too long", current($messages));
    }

    /**
     * Ensures that we can include another parameter, defined on a
     * class-by-class basis, in the message string.
     * In the case of Zend_Validate_StringLength, one such parameter
     * is %max%.
     */
    public function testSetMessageWithOtherParam()
    {
        $this->_validator->setMessage(
            'Your value is too long, it should be no longer than %max%',
            Zend_Validate_StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long, it should be no longer than 8', current($messages));
    }

    /**
     * Ensures that if we set a parameter in the message that is not
     * known to the validator class, it is not changed; %shazam% is
     * left as literal text in the message.
     */
    public function testSetMessageWithUnknownParam()
    {
        $this->_validator->setMessage(
            'Your value is too long, and btw, %shazam%!',
            Zend_Validate_StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long, and btw, %shazam%!', current($messages));
    }

    /**
     * Ensures that the validator throws an exception when we
     * try to set a message for a key that is unknown to the class.
     */
    public function testSetMessageExceptionInvalidKey()
    {
        $keyInvalid = 'invalidKey';

        try {
            $this->_validator->setMessage(
                'Your value is too long',
                $keyInvalid
            );
            $this->fail('Expected to catch Zend_Validate_Exception');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Validate_Exception,
                'Expected exception of type Zend_Validate_Exception, got ' . get_class($e));
            $this->assertEquals("No message template exists for key '$keyInvalid'", $e->getMessage());
        }
    }

    /**
     * Ensures that we can set more than one message at a time,
     * by passing an array of key/message pairs.  Both messages
     * should be defined.
     */
    public function testSetMessages()
    {
        $this->_validator->setMessages(
            [
                Zend_Validate_StringLength::TOO_LONG => 'Your value is too long',
                Zend_Validate_StringLength::TOO_SHORT => 'Your value is too short',
            ]
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too short', current($messages));
    }

    /**
     * Ensures that the magic getter gives us access to properties
     * that are permitted to be substituted in the message string.
     * The access is by the parameter name, not by the protected
     * property variable name.
     */
    public function testGetProperty()
    {
        $this->_validator->setMessage(
            'Your value is too long',
            Zend_Validate_StringLength::TOO_LONG
        );

        $inputInvalid = 'abcdefghij';

        $this->assertFalse($this->_validator->isValid($inputInvalid));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        $this->assertEquals($inputInvalid, $this->_validator->value);
        $this->assertEquals(8, $this->_validator->max);
        $this->assertEquals(4, $this->_validator->min);
    }

    /**
     * Ensures that the class throws an exception when we try to
     * access a property that doesn't exist as a parameter.
     */
    public function testGetPropertyException()
    {
        $this->_validator->setMessage(
            'Your value is too long',
            Zend_Validate_StringLength::TOO_LONG
        );

        $this->assertFalse($this->_validator->isValid('abcdefghij'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('Your value is too long', current($messages));

        try {
            $property = $this->_validator->unknownProperty;
            $this->fail('Expected to catch Zend_Validate_Exception');
        } catch (Zend_Exception $e) {
            static::assertTrue($e instanceof Zend_Validate_Exception, 'Expected exception of type Zend_Validate_Exception, got ' . get_class($e));
            static::assertEquals("No property exists by the name 'unknownProperty'", $e->getMessage());
        }
    }

    /**
     * Ensures that the getError() function returns an array of
     * message key values corresponding to the messages.
     */
    public function testGetErrors()
    {
        $inputInvalid = 'abcdefghij';
        $this->assertFalse($this->_validator->isValid($inputInvalid));

        $messages = $this->_validator->getMessages();
        $this->assertEquals("'$inputInvalid' is more than 8 characters long", current($messages));

        $errors = $this->_validator->getErrors();
        $this->assertEquals(Zend_Validate_StringLength::TOO_LONG, current($errors));
    }

    /**
     * Ensures that getMessageVariables() returns an array of
     * strings and that these strings that can be used as variables
     * in a message.
     */
    public function testGetMessageVariables()
    {
        $vars = $this->_validator->getMessageVariables();

        $this->assertTrue(is_array($vars));
        $this->assertEquals(['min', 'max'], $vars);
        $message = 'variables: %notvar% ';
        foreach ($vars as $var) {
            $message .= "%$var% ";
        }
        $this->_validator->setMessage($message, Zend_Validate_StringLength::TOO_SHORT);

        $this->assertFalse($this->_validator->isValid('abc'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals('variables: %notvar% 4 8 ', current($messages));
    }
}

// Call Zend_Validate_MessageTest::main() if this source file is executed directly.
