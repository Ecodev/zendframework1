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
 * @version    $Id: $
 */

/**
 * @see Zend_Filter_Compress_Zip
 */
require_once 'Zend/Filter/Compress/Zip.php';

/**
 * @group      Zend_Filter
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Filter_Compress_ZipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Filter_Compress_ZipTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        if (!extension_loaded('zip')) {
            $this->markTestSkipped('This adapter needs the zip extension');
        }

        $files = [
            __DIR__ . '/../_files/compressed.zip',
            __DIR__ . '/../_files/zipextracted.txt',
            __DIR__ . '/../_files/zip.tmp',
            __DIR__ . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress/First/Second',
            __DIR__ . '/../_files/_compress/Compress/First/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress/First',
            __DIR__ . '/../_files/_compress/Compress/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress',
            __DIR__ . '/../_files/_compress/zipextracted.txt',
            __DIR__ . '/../_files/_compress',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(__DIR__ . '/../_files/Compress/First/Second')) {
            mkdir(__DIR__ . '/../_files/Compress/First/Second', 0o777, true);
            file_put_contents(__DIR__ . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(__DIR__ . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(__DIR__ . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    public function tearDown(): void
    {
        $files = [
            __DIR__ . '/../_files/compressed.zip',
            __DIR__ . '/../_files/zipextracted.txt',
            __DIR__ . '/../_files/zip.tmp',
            __DIR__ . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress/First/Second',
            __DIR__ . '/../_files/_compress/Compress/First/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress/First',
            __DIR__ . '/../_files/_compress/Compress/zipextracted.txt',
            __DIR__ . '/../_files/_compress/Compress',
            __DIR__ . '/../_files/_compress/zipextracted.txt',
            __DIR__ . '/../_files/_compress',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(__DIR__ . '/../_files/Compress/First/Second')) {
            mkdir(__DIR__ . '/../_files/Compress/First/Second', 0o777, true);
            file_put_contents(__DIR__ . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(__DIR__ . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(__DIR__ . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files/zipextracted.txt',
            ]
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(__DIR__ . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options.
     */
    public function testZipGetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals(['archive' => null, 'target' => null], $filter->getOptions());

        $this->assertEquals(null, $filter->getOptions('archive'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(['nooption' => 'foo']);
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(['archive' => 'temp.txt']);
        $this->assertEquals('temp.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive.
     */
    public function testZipGetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Target.
     */
    public function testZipGetSetTarget()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertNull($filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        try {
            $filter->setTarget('/unknown/path/to/file.txt');
            $this->fails('Exception expected');
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('does not exist', $e->getMessage());
        }
    }

    /**
     * Compress to Archive.
     */
    public function testZipCompressFile()
    {
        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files/zipextracted.txt',
            ]
        );
        file_put_contents(__DIR__ . '/../_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(__DIR__ . '/../_files/zipextracted.txt');
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(__DIR__ . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Basic usage.
     */
    public function testCompressNonExistingTargetFile()
    {
        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files',
            ]
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(__DIR__ . '/../_files/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Archive.
     */
    public function testZipCompressDirectory()
    {
        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files/_compress',
            ]
        );
        $content = $filter->compress(__DIR__ . '/../_files/Compress');
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        mkdir(__DIR__ . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress');
        $content = $filter->decompress($content);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress'
                            . DIRECTORY_SEPARATOR, $content);

        $base = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
              . DIRECTORY_SEPARATOR . '_compress' . DIRECTORY_SEPARATOR . 'Compress' . DIRECTORY_SEPARATOR;
        $this->assertTrue(file_exists($base));
        $this->assertTrue(file_exists($base . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR
                          . 'Second' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $content = file_get_contents(__DIR__ . '/../_files/Compress/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * testing toString.
     */
    public function testZipToString()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals('Zip', $filter->toString());
    }

    /**
     * @group
     */
    public function testDecompressWillThrowExceptionWhenDecompressingWithNoTarget()
    {
        $this->expectException(\Zend_Filter_Exception::class);
        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files/_compress',
            ]
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
            ]
        );
        $content = $filter->decompress($content);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(__DIR__ . '/../_files/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * @group RS
     */
    public function testDecompressWillThrowExceptionWhenDetectingUpwardDirectoryTraversal()
    {
        $this->expectException(\Zend_Filter_Exception::class);
        if (version_compare(PHP_VERSION, '5.2.8', '>=')) {
            $this->markTestSkipped('This test is to run on PHP less than 5.2.8');

            return;
        }

        $filter = new Zend_Filter_Compress_Zip(
            [
                'archive' => __DIR__ . '/../_files/compressed.zip',
                'target' => __DIR__ . '/../_files/evil.zip',
            ]
        );

        $filter->decompress(__DIR__ . '/../_files/evil.zip');
    }
}
