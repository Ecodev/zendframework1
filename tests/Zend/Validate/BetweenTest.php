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
 * @see Zend_Validate_Between
 */
require_once 'Zend/Validate/Between.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_BetweenTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - minimum
         *      - maximum
         *      - inclusive
         *      - expected validation result
         *      - array of test input values.
         */
        $valuesExpected = [
            [1, 100, true, true, [1, 10, 100]],
            [1, 100, true, false, [0, 0.99, 100.01, 101]],
            [1, 100, false, false, [0, 1, 100, 101]],
            ['a', 'z', true, true, ['a', 'b', 'y', 'z']],
            ['a', 'z', false, false, ['!', 'a', 'z']],
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Between(['min' => $element[0], 'max' => $element[1], 'inclusive' => $element[2]]);
            foreach ($element[4] as $input) {
                $this->assertEquals($element[3], $validator->isValid($input),
                    'Failed values: ' . $input . ':' . implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value.
     */
    public function testGetMessages()
    {
        $validator = new Zend_Validate_Between(['min' => 1, 'max' => 10]);
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that getMin() returns expected value.
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(1, $validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected value.
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that getInclusive() returns expected default value.
     */
    public function testGetInclusive()
    {
        $validator = new Zend_Validate_Between(['min' => 1, 'max' => 10]);
        $this->assertEquals(true, $validator->getInclusive());
    }
}
