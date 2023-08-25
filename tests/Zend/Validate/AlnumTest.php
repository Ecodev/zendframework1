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
 * @see Zend_Validate_Alnum
 */
require_once 'Zend/Validate/Alnum.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_AlnumTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Validate_Alnum object.
     *
     * @var Zend_Validate_Alnum
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Alnum object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_Alnum();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values.
     */
    public function testExpectedResultsWithBasicInputValues()
    {
        $valuesExpected = [
            'abc123' => true,
            'abc 123' => false,
            'abcxyz' => true,
            'AZ@#4.3' => false,
            'aBc123' => true,
            '' => false,
            ' ' => false,
            "\n" => false,
            'foobar1' => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value.
     */
    public function testMessagesEmptyInitially()
    {
        $this->assertEquals([], $this->_validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected.
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues()
    {
        $this->_validator->setAllowWhiteSpace(true);

        $valuesExpected = [
            'abc123' => true,
            'abc 123' => true,
            'abcxyz' => true,
            'AZ@#4.3' => false,
            'aBc123' => true,
            '' => false,
            ' ' => true,
            "\n" => true,
            " \t " => true,
            'foobar1' => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->_validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
                );
        }
    }

    public function testEmptyStringValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->_validator->isValid(''));
        $messages = $this->_validator->getMessages();
        $arrayExpected = [
            Zend_Validate_Alnum::STRING_EMPTY => '\'\' is an empty string',
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @deprecated Since 1.5.0
     */
    public function testEmptyStringValueResultsInProperValidationFailureErrors()
    {
        $this->assertFalse($this->_validator->isValid(''));
        $errors = $this->_validator->getErrors();
        $arrayExpected = [
            Zend_Validate_Alnum::STRING_EMPTY,
        ];
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    public function testInvalidValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $messages = $this->_validator->getMessages();
        $arrayExpected = [
            Zend_Validate_Alnum::NOT_ALNUM => '\'#\' contains characters which are non alphabetic and no digits',
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @deprecated Since 1.5.0
     */
    public function testInvalidValueResultsInProperValidationFailureErrors()
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $errors = $this->_validator->getErrors();
        $arrayExpected = [
            Zend_Validate_Alnum::NOT_ALNUM,
        ];
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid([1 => 1]));
    }

    /**
     * @ZF-7475
     */
    public function testIntegerValidation()
    {
        $this->assertTrue($this->_validator->isValid(1));
    }
}
