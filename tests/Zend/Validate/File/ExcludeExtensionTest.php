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
 * @see Zend_Validate_File_ExcludeExtension
 */
require_once 'Zend/Validate/File/ExcludeExtension.php';

/**
 * ExcludeExtension testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Validate')]
class Zend_Validate_File_ExcludeExtensionTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_Validate_File_ExcludeExtensionTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            ['mo', false],
            ['gif', true],
            [['mo'], false],
            [['gif'], true],
            [['gif', 'pdf', 'mo', 'pict'], false],
            [['gif', 'gz', 'hint'], true],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ExcludeExtension($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                'Tested with ' . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileExcludeExtensionNotFound', $validator->getMessages()));

        $files = [
            'name' => 'test1',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => 'tmp_test1',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileExcludeExtensionNotFound', $validator->getMessages()));

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
        $this->assertTrue(array_key_exists('fileExcludeExtensionFalse', $validator->getMessages()));

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_ExcludeExtension('gif');
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    public function testCaseTesting()
    {
        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_ExcludeExtension(['MO', 'case' => true]);
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));

        $validator = new Zend_Validate_File_ExcludeExtension(['MO', 'case' => false]);
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    /**
     * Ensures that getExtension() returns expected value.
     */
    public function testGetExtension()
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $this->assertEquals(['mo'], $validator->getExtension());

        $validator = new Zend_Validate_File_ExcludeExtension(['mo', 'gif', 'jpg']);
        $this->assertEquals(['mo', 'gif', 'jpg'], $validator->getExtension());
    }

    /**
     * Ensures that setExtension() returns expected value.
     */
    public function testSetExtension()
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $validator->setExtension('gif');
        $this->assertEquals(['gif'], $validator->getExtension());

        $validator->setExtension('jpg, mo');
        $this->assertEquals(['jpg', 'mo'], $validator->getExtension());

        $validator->setExtension(['zip', 'ti']);
        $this->assertEquals(['zip', 'ti'], $validator->getExtension());
    }

    /**
     * Ensures that addExtension() returns expected value.
     */
    public function testAddExtension()
    {
        $validator = new Zend_Validate_File_ExcludeExtension('mo');
        $validator->addExtension('gif');
        $this->assertEquals(['mo', 'gif'], $validator->getExtension());

        $validator->addExtension('jpg, to');
        $this->assertEquals(['mo', 'gif', 'jpg', 'to'], $validator->getExtension());

        $validator->addExtension(['zip', 'ti']);
        $this->assertEquals(['mo', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getExtension());

        $validator->addExtension('');
        $this->assertEquals(['mo', 'gif', 'jpg', 'to', 'zip', 'ti'], $validator->getExtension());
    }
}
