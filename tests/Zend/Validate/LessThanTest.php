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
 * @see Zend_Validate_LessThan
 */
require_once 'Zend/Validate/LessThan.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_LessThanTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - maximum
         *      - expected validation result
         *      - array of test input values.
         */
        $valuesExpected = [
            [100, true, [-1, 0, 0.01, 1, 99.999]],
            [100, false, [100, 100.0, 100.01]],
            ['a', false, ['a', 'b', 'c', 'd']],
            ['z', true, ['x', 'y']],
        ];
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_LessThan($element[0]);
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
        $validator = new Zend_Validate_LessThan(10);
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that getMax() returns expected value.
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_LessThan(10);
        $this->assertEquals(10, $validator->getMax());
    }
}
