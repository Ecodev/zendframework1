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
 * @see Zend_Validate_Hex
 */
require_once 'Zend/Validate/Hex.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_HexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Validate_Hex object.
     *
     * @var Zend_Validate_Hex
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Hex object for each test method.
     */
    public function setUp(): void
    {
        $this->_validator = new Zend_Validate_Hex();
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            [1, true],
            [0x1, true],
            [0x123, true],
            ['1', true],
            ['abc123', true],
            ['ABC123', true],
            ['1234567890abcdef', true],
            ['g', false],
            ['1.2', false],
        ];
        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]), $element[0]);
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
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid([1 => 1]));
    }
}
