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
 * @see Zend_Validate_File_Size
 */
require_once 'Zend/Validate/File/Size.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_SizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_SizeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            [['min' => 0, 'max' => 10000], true],
            [['min' => 0, 'max' => '10 MB'], true],
            [['min' => '4B', 'max' => '10 MB'], true],
            [['min' => 0, 'max' => '10MB'], true],
            [['min' => 0, 'max' => '10  MB'], true],
            [794, true],
            [['min' => 794], true],
            [['min' => 0, 'max' => 500], false],
            [500, false],
        ];

        foreach ($valuesExpected as $element) {
            $options = array_shift($element);
            $value = array_shift($element);
            $validator = new Zend_Validate_File_Size($options);
            $this->assertEquals(
                $value,
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                'Tested ' . var_export($value, 1) . ' against options ' . var_export($options, 1)
            );
        }
    }

    /**
     * Ensures that getMin() returns expected value.
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_Size(['min' => 1, 'max' => 100]);
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_Size(['min' => 100, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(['min' => 1, 'max' => 100, 'bytestring' => false]);
        $this->assertEquals(1, $validator->getMin());
    }

    /**
     * Ensures that setMin() returns expected value.
     */
    public function testSetMin()
    {
        $validator = new Zend_Validate_File_Size(['min' => 1000, 'max' => 10000]);
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('less than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(['min' => 1000, 'max' => 10000, 'bytestring' => false]);
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected value.
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_File_Size(['min' => 1, 'max' => 100, 'bytestring' => false]);
        $this->assertEquals(100, $validator->getMax());

        try {
            $validator = new Zend_Validate_File_Size(['min' => 100, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_Size(['min' => 1, 'max' => 100000]);
        $this->assertEquals('97.66kB', $validator->getMax());

        $validator = new Zend_Validate_File_Size(2000);
        $this->assertEquals('1.95kB', $validator->getMax());
    }

    /**
     * Ensures that setMax() returns expected value.
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_Size(['max' => 0, 'bytestring' => true]);
        $this->assertEquals('0B', $validator->getMax());

        $validator->setMax(1_000_000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMax('100 AB');
        $this->assertEquals('100B', $validator->getMax());

        $validator->setMax('100 kB');
        $this->assertEquals('100kB', $validator->getMax());

        $validator->setMax('100 MB');
        $this->assertEquals('100MB', $validator->getMax());

        $validator->setMax('1 GB');
        $this->assertEquals('1GB', $validator->getMax());

        $validator->setMax('0.001 TB');
        $this->assertEquals('1.02GB', $validator->getMax());

        $validator->setMax('0.000001 PB');
        $this->assertEquals('1.05GB', $validator->getMax());

        $validator->setMax('0.000000001 EB');
        $this->assertEquals('1.07GB', $validator->getMax());

        $validator->setMax('0.000000000001 ZB');
        $this->assertEquals('1.1GB', $validator->getMax());

        $validator->setMax('0.000000000000001 YB');
        $this->assertEquals('1.13GB', $validator->getMax());
    }

    /**
     * Ensures that the validator returns size infos.
     */
    public function testFailureMessage()
    {
        $validator = new Zend_Validate_File_Size(['min' => 9999, 'max' => 10000]);
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/testsize.mo'));
        $this->assertStringContainsString('9.76kB', current($validator->getMessages()));
        $this->assertStringContainsString('794B', current($validator->getMessages()));

        $validator = new Zend_Validate_File_Size(['min' => 9999, 'max' => 10000, 'bytestring' => false]);
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/testsize.mo'));
        $this->assertStringContainsString('9999', current($validator->getMessages()));
        $this->assertStringContainsString('794', current($validator->getMessages()));
    }
}
