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
 * @see Zend_Validate_Regex
 */
require_once 'Zend/Validate/Regex.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_RegexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - pattern
         *      - expected validation result
         *      - array of test input values.
         */
        $valuesExpected = [
            ['/[a-z]/', true, ['abc123', 'foo', 'a', 'z']],
            ['/[a-z]/', false, ['123', 'A']],
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Regex($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value.
     */
    public function testGetMessages()
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that getPattern() returns expected value.
     */
    public function testGetPattern()
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertEquals('/./', $validator->getPattern());
    }

    /**
     * Ensures that a bad pattern results in a thrown exception upon isValid() call.
     */
    public function testBadPattern()
    {
        try {
            $validator = new Zend_Validate_Regex('/');
            $validator->isValid('anything');
            $this->fail('Expected Zend_Validate_Exception not thrown for bad pattern');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('Internal error while', $e->getMessage());
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $validator = new Zend_Validate_Regex('/./');
        $this->assertFalse($validator->isValid([1 => 1]));
    }
}
