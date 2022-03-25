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
 * @see Zend_Validate_File_ExcludeMimeType
 */
require_once 'Zend/Validate/File/ExcludeMimeType.php';

/**
 * ExcludeMimeType testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_ExcludeMimeTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_ExcludeMimeTypeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        $valuesExpected = [
            ['image/gif', true],
            ['image/jpeg', false],
            ['image', false],
            ['test/notype', true],
            ['image/gif, image/jpeg', false],
            [['image/vasa', 'image/jpeg'], false],
            [['image/gif', 'jpeg'], false],
            [['image/gif', 'gif'], true],
        ];

        $filetest = __DIR__ . '/_files/picture.jpg';
        $files = [
            'name' => 'picture.jpg',
            'type' => 'image/jpeg',
            'size' => 200,
            'tmp_name' => $filetest,
            'error' => 0,
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ExcludeMimeType($element[0]);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $element[1],
                $validator->isValid($filetest, $files),
                'Tested with ' . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMimeType() returns expected value.
     */
    public function testGetMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(['image/gif', 'video', 'text/test']);
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(['image/gif', 'video', 'text/test']);
        $this->assertEquals(['image/gif', 'video', 'text/test'], $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value.
     */
    public function testSetMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->setMimeType('image/jpeg');
        $this->assertEquals('image/jpeg', $validator->getMimeType());
        $this->assertEquals(['image/jpeg'], $validator->getMimeType(true));

        $validator->setMimeType('image/gif, text/test');
        $this->assertEquals('image/gif,text/test', $validator->getMimeType());
        $this->assertEquals(['image/gif', 'text/test'], $validator->getMimeType(true));

        $validator->setMimeType(['video/mpeg', 'gif']);
        $this->assertEquals('video/mpeg,gif', $validator->getMimeType());
        $this->assertEquals(['video/mpeg', 'gif'], $validator->getMimeType(true));
    }

    /**
     * Ensures that addMimeType() returns expected value.
     */
    public function testAddMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->addMimeType('text');
        $this->assertEquals('image/gif,text', $validator->getMimeType());
        $this->assertEquals(['image/gif', 'text'], $validator->getMimeType(true));

        $validator->addMimeType('jpg, to');
        $this->assertEquals('image/gif,text,jpg,to', $validator->getMimeType());
        $this->assertEquals(['image/gif', 'text', 'jpg', 'to'], $validator->getMimeType(true));

        $validator->addMimeType(['zip', 'ti']);
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(['image/gif', 'text', 'jpg', 'to', 'zip', 'ti'], $validator->getMimeType(true));

        $validator->addMimeType('');
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(['image/gif', 'text', 'jpg', 'to', 'zip', 'ti'], $validator->getMimeType(true));
    }

    /**
     * Ensure validator is not affected by PHP bug #63976.
     */
    public function testShouldHaveProperErrorMessageOnNotReadableFile()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/jpeg');

        $this->assertFalse($validator->isValid('notexisting'));
        $this->assertEquals(
            ['fileExcludeMimeTypeNotReadable' => "File 'notexisting' is not readable or does not exist"],
            $validator->getMessages()
        );
    }
}
