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
require_once 'Zend/Validate/File/Exists.php';

/**
 * Exists testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_ExistsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_ExistsTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $baseDir = __DIR__;
        $valuesExpected = [
            [$baseDir, 'testsize.mo', false],
            [$baseDir . '/_files', 'testsize.mo', true],
        ];

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Exists($element[0]);
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                'Tested with ' . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1], $files),
                'Tested with ' . var_export($element, 1)
            );
        }

        $valuesExpected = [
            [$baseDir, 'testsize.mo', false],
            [$baseDir . '/_files', 'testsize.mo', true],
        ];

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
            'destination' => __DIR__ . '/_files',
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Exists($element[0]);
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                'Tested with ' . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1], $files),
                'Tested with ' . var_export($element, 1)
            );
        }

        $valuesExpected = [
            [$baseDir, 'testsize.mo', false, true],
            [$baseDir . '/_files', 'testsize.mo', false, true],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Exists();
            $this->assertEquals(
                $element[2],
                $validator->isValid($element[1]),
                'Tested with ' . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid($element[1], $files),
                'Tested with ' . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getDirectory() returns expected value.
     */
    public function testGetDirectory()
    {
        $validator = new Zend_Validate_File_Exists('C:/temp');
        $this->assertEquals('C:/temp', $validator->getDirectory());

        $validator = new Zend_Validate_File_Exists(['temp', 'dir', 'jpg']);
        $this->assertEquals('temp,dir,jpg', $validator->getDirectory());

        $validator = new Zend_Validate_File_Exists(['temp', 'dir', 'jpg']);
        $this->assertEquals(['temp', 'dir', 'jpg'], $validator->getDirectory(true));
    }

    /**
     * Ensures that setDirectory() returns expected value.
     */
    public function testSetDirectory()
    {
        $validator = new Zend_Validate_File_Exists('temp');
        $validator->setDirectory('gif');
        $this->assertEquals('gif', $validator->getDirectory());
        $this->assertEquals(['gif'], $validator->getDirectory(true));

        $validator->setDirectory('jpg, temp');
        $this->assertEquals('jpg,temp', $validator->getDirectory());
        $this->assertEquals(['jpg', 'temp'], $validator->getDirectory(true));

        $validator->setDirectory(['zip', 'ti']);
        $this->assertEquals('zip,ti', $validator->getDirectory());
        $this->assertEquals(['zip', 'ti'], $validator->getDirectory(true));
    }

    /**
     * Ensures that addDirectory() returns expected value.
     */
    public function testAddDirectory()
    {
        $validator = new Zend_Validate_File_Exists('temp');
        $validator->addDirectory('gif');
        $this->assertEquals('temp,gif', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif'], $validator->getDirectory(true));

        $validator->addDirectory('jpg, to');
        $this->assertEquals('temp,gif,jpg,to', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to'], $validator->getDirectory(true));

        $validator->addDirectory(['zip', 'ti']);
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getDirectory(true));

        $validator->addDirectory('');
        $this->assertEquals('temp,gif,jpg,to,zip,ti', $validator->getDirectory());
        $this->assertEquals(['temp', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getDirectory(true));
    }
}
