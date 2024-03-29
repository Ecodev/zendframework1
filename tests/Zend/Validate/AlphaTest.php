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
 * @see Zend_Validate_Alpha
 */
require_once 'Zend/Validate/Alpha.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_AlphaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Validate_Alpha object.
     *
     * @var Zend_Validate_Alpha
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Alpha object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_Alpha();
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            'abc123' => false,
            'abc 123' => false,
            'abcxyz' => true,
            'AZ@#4.3' => false,
            'aBc123' => false,
            'aBcDeF' => true,
            '' => false,
            ' ' => false,
            "\n" => false,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
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
     * Ensures that the allowWhiteSpace option works as expected.
     */
    public function testAllowWhiteSpace()
    {
        $this->_validator->setAllowWhiteSpace(true);

        $valuesExpected = [
            'abc123' => false,
            'abc 123' => false,
            'abcxyz' => true,
            'AZ@#4.3' => false,
            'aBc123' => false,
            'aBcDeF' => true,
            '' => false,
            ' ' => true,
            "\n" => true,
            " \t " => true,
            "a\tb c" => true,
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->_validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
            );
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid([1 => 1]));
    }
}
