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
 * @see Zend_Validate_File_IsCompressed
 */
require_once 'Zend/Validate/File/IsCompressed.php';

/**
 * IsCompressed testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Validate
 */
#[AllowDynamicProperties]
class Zend_Validate_File_IsCompressedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Validate_File_IsCompressedTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior.
     */
    public function testBasic()
    {
        if (!extension_loaded('fileinfo')
            && function_exists('mime_content_type') && ini_get('mime_magic.magicfile')
            && (mime_content_type(__DIR__ . '/_files/test.zip') == 'text/plain')
        ) {
            $this->markTestSkipped('This PHP Version has no finfo, has mime_content_type, '
                . ' but mime_content_type exhibits buggy behavior on this system.'
            );
        }

        // Prevent error in the next check
        if (!function_exists('mime_content_type')) {
            $this->markTestSkipped('mime_content_type function is not available.');
        }

        // Sometimes mime_content_type() gives application/zip and sometimes
        // application/x-zip ...
        $expectedMimeType = mime_content_type(__DIR__ . '/_files/test.zip');
        if (!in_array($expectedMimeType, ['application/zip', 'application/x-zip'])) {
            $this->markTestSkipped('mime_content_type exhibits buggy behavior on this system!');
        }

        $valuesExpected = [
            [null, true],
            ['zip', true],
            ['test/notype', false],
            ['application/x-zip, application/zip, application/x-tar', true],
            [['application/x-zip', 'application/zip', 'application/x-tar'], true],
            [['zip', 'tar'], true],
            [['tar', 'arj'], false],
        ];

        $files = [
            'name' => 'test.zip',
            'type' => $expectedMimeType,
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/test.zip',
            'error' => 0,
        ];

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_IsCompressed($element[0]);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/test.zip', $files),
                'Tested with ' . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMimeType() returns expected value.
     */
    public function testGetMimeType()
    {
        $validator = new Zend_Validate_File_IsCompressed('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new Zend_Validate_File_IsCompressed(['image/gif', 'video', 'text/test']);
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new Zend_Validate_File_IsCompressed(['image/gif', 'video', 'text/test']);
        $this->assertEquals(['image/gif', 'video', 'text/test'], $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value.
     */
    public function testSetMimeType()
    {
        $validator = new Zend_Validate_File_IsCompressed('image/gif');
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
        $validator = new Zend_Validate_File_IsCompressed('image/gif');
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
     * @ZF-8111
     */
    public function testErrorMessages()
    {
        $files = [
            'name' => 'picture.jpg',
            'type' => 'image/jpeg',
            'size' => 200,
            'tmp_name' => __DIR__ . '/_files/picture.jpg',
            'error' => 0,
        ];

        $validator = new Zend_Validate_File_IsCompressed('test/notype');
        $validator->enableHeaderCheck();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $error = $validator->getMessages();
        $this->assertTrue(array_key_exists('fileIsCompressedFalseType', $error));
    }

    public function testOptionsAtConstructor()
    {
        if (!extension_loaded('fileinfo')) {
            $this->markTestSkipped('This PHP Version has no finfo installed');
        }

        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            $magicFile = __DIR__ . '/_files/magic-php53.mime';
        } else {
            $magicFile = __DIR__ . '/_files/magic.mime';
        }

        $validator = new Zend_Validate_File_IsCompressed([
            'image/gif',
            'image/jpg',
            'magicfile' => $magicFile,
            'headerCheck' => true, ]);

        $this->assertEquals($magicFile, $validator->getMagicFile());
        $this->assertTrue($validator->getHeaderCheck());
        $this->assertEquals('image/gif,image/jpg', $validator->getMimeType());
    }
}
