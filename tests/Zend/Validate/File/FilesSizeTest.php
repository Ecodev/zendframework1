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
 * @see Zend_Validate_File_FilesSize
 */
require_once 'Zend/Validate/File/FilesSize.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_FilesSizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_FilesSizeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $this->multipleOptionsDetected = false;
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            [['min' => 0, 'max' => 2000], true, true, false],
            [['min' => 0, 'max' => '2 MB'], true, true, true],
            [['min' => 0, 'max' => '2MB'], true, true, true],
            [['min' => 0, 'max' => '2  MB'], true, true, true],
            [2000, true, true, false],
            [['min' => 0, 'max' => 500], false, false, false],
            [500, false, false, false],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_FilesSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                'Tested with ' . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid(__DIR__ . '/_files/testsize2.mo'),
                'Tested with ' . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid(__DIR__ . '/_files/testsize3.mo'),
                'Tested with ' . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_FilesSize(['min' => 0, 'max' => 200]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileFilesSizeNotReadable', $validator->getMessages()));

        $validator = new Zend_Validate_File_FilesSize(['min' => 0, 'max' => 500000]);
        $this->assertEquals(true, $validator->isValid([
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo', ]));
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo'));
    }

    /**
     * Ensures that getMin() returns expected value.
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_FilesSize(['min' => 1, 'max' => 100]);
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_FilesSize(['min' => 100, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(['min' => 1, 'max' => 100]);
        $this->assertEquals('1B', $validator->getMin());
    }

    /**
     * Ensures that setMin() returns expected value.
     */
    public function testSetMin()
    {
        $validator = new Zend_Validate_File_FilesSize(['min' => 1000, 'max' => 10000]);
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

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
        $validator = new Zend_Validate_File_FilesSize(['min' => 1, 'max' => 100]);
        $this->assertEquals('100B', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_FilesSize(['min' => 100, 'max' => 1]);
            $this->fail('Missing exception');
        } catch (Zend_Validate_Exception $e) {
            $this->assertStringContainsString('greater than or equal', $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(['min' => 1, 'max' => 100000]);
        $this->assertEquals('97.66kB', $validator->getMax());

        $validator = new Zend_Validate_File_FilesSize(2000);
        $validator->setUseByteString(false);
        $test = $validator->getMax();
        $this->assertEquals('2000', $test);
    }

    /**
     * Ensures that setMax() returns expected value.
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_FilesSize(['min' => 1000, 'max' => 10000]);
        $validator->setMax(1_000_000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());
    }

    public function testConstructorShouldRaiseErrorWhenPassedMultipleOptions()
    {
        $handler = set_error_handler([$this, 'errorHandler'], E_USER_NOTICE);
        $validator = new Zend_Validate_File_FilesSize(1000, 10000);
        restore_error_handler();
        self::assertTrue(true);
    }

    /**
     * Ensures that the validator returns size infos.
     */
    public function testFailureMessage()
    {
        $validator = new Zend_Validate_File_FilesSize(['min' => 9999, 'max' => 10000]);
        $this->assertFalse($validator->isValid([
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo', ]));
        $this->assertStringContainsString('9.76kB', current($validator->getMessages()));
        $this->assertStringContainsString('1.55kB', current($validator->getMessages()));

        $validator = new Zend_Validate_File_FilesSize(['min' => 9999, 'max' => 10000, 'bytestring' => false]);
        $this->assertFalse($validator->isValid([
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize.mo',
            __DIR__ . '/_files/testsize2.mo', ]));
        $this->assertStringContainsString('9999', current($validator->getMessages()));
        $this->assertStringContainsString('1588', current($validator->getMessages()));
    }

    public function errorHandler($errno, $errstr)
    {
        if (strstr($errstr, 'deprecated')) {
            $this->multipleOptionsDetected = true;
        }
    }
}
