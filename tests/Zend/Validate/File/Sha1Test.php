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
 * @see Zend_Validate_File_Sha1
 */
require_once 'Zend/Validate/File/Sha1.php';

/**
 * Sha1 testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_Sha1Test extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_Sha1Test');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            ['b2a5334847b4328e7d19d9b41fd874dffa911c98', true],
            ['52a5334847b4328e7d19d9b41fd874dffa911c98', false],
            [['42a5334847b4328e7d19d9b41fd874dffa911c98', 'b2a5334847b4328e7d19d9b41fd874dffa911c98'], true],
            [['42a5334847b4328e7d19d9b41fd874dffa911c98', '72a5334847b4328e7d19d9b41fd874dffa911c98'], false],
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Sha1($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                'Tested with ' . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = [
            'name' => 'test1',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => 'tmp_test1',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertTrue($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));

        $files = [
            'name' => 'testsize.mo',
            'type' => 'text',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error' => 0,
        ];
        $validator = new Zend_Validate_File_Sha1('42a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileSha1DoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getSha1() returns expected value.
     */
    public function testgetSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $this->assertEquals(['12345' => 'sha1'], $validator->getSha1());

        $validator = new Zend_Validate_File_Sha1(['12345', '12333', '12344']);
        $this->assertEquals(['12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'], $validator->getSha1());
    }

    /**
     * Ensures that getHash() returns expected value.
     */
    public function testgetHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $this->assertEquals(['12345' => 'sha1'], $validator->getHash());

        $validator = new Zend_Validate_File_Sha1(['12345', '12333', '12344']);
        $this->assertEquals(['12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'], $validator->getHash());
    }

    /**
     * Ensures that setSha1() returns expected value.
     */
    public function testSetSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->setSha1('12333');
        $this->assertEquals(['12333' => 'sha1'], $validator->getSha1());

        $validator->setSha1(['12321', '12121']);
        $this->assertEquals(['12321' => 'sha1', '12121' => 'sha1'], $validator->getSha1());
    }

    /**
     * Ensures that setHash() returns expected value.
     */
    public function testSetHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->setHash('12333');
        $this->assertEquals(['12333' => 'sha1'], $validator->getSha1());

        $validator->setHash(['12321', '12121']);
        $this->assertEquals(['12321' => 'sha1', '12121' => 'sha1'], $validator->getSha1());
    }

    /**
     * Ensures that addSha1() returns expected value.
     */
    public function testAddSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->addSha1('12344');
        $this->assertEquals(['12345' => 'sha1', '12344' => 'sha1'], $validator->getSha1());

        $validator->addSha1(['12321', '12121']);
        $this->assertEquals(['12345' => 'sha1', '12344' => 'sha1', '12321' => 'sha1', '12121' => 'sha1'], $validator->getSha1());
    }

    /**
     * Ensures that addHash() returns expected value.
     */
    public function testAddHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->addHash('12344');
        $this->assertEquals(['12345' => 'sha1', '12344' => 'sha1'], $validator->getSha1());

        $validator->addHash(['12321', '12121']);
        $this->assertEquals(['12345' => 'sha1', '12344' => 'sha1', '12321' => 'sha1', '12121' => 'sha1'], $validator->getSha1());
    }
}
