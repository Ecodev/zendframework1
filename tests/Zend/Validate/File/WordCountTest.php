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
 * @see Zend_Validate_File_WordCount
 */
require_once 'Zend/Validate/File/WordCount.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_WordCountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_WordCountTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            [15, true],
            [4, false],
            [['min' => 0, 'max' => 10], true],
            [['min' => 10, 'max' => 15], false],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_WordCount($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/wordcount.txt'),
                'Tested with ' . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMin() returns expected value.
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_WordCount(['min' => 1, 'max' => 5]);
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_WordCount(['min' => 5, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_WordCount(['min' => 1, 'max' => 5]);
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new Zend_Validate_File_WordCount(['min' => 5, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that setMin() returns expected value.
     */
    public function testSetMin()
    {
        $validator = new Zend_Validate_File_WordCount(['min' => 1000, 'max' => 10000]);
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('less than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that getMax() returns expected value.
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_File_WordCount(['min' => 1, 'max' => 100]);
        $this->assertEquals(100, $validator->getMax());

        try {
            $validator = new Zend_Validate_File_WordCount(['min' => 5, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }
    }

    /**
     * Ensures that setMax() returns expected value.
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_WordCount(['min' => 1000, 'max' => 10000]);
        $validator->setMax(1_000_000);
        $this->assertEquals(1_000_000, $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals(1_000_000, $validator->getMax());
    }
}
