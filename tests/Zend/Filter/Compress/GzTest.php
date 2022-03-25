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
 * @see Zend_Filter_Compress_Gz
 */
require_once 'Zend/Filter/Compress/Gz.php';

/**
 * @group      Zend_Filter
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_GzTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Filter_Compress_GzTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This adapter needs the zlib extension');
        }
    }

    public function tearDown(): void
    {
        if (file_exists(__DIR__ . '/../_files/compressed.gz')) {
            unlink(__DIR__ . '/../_files/compressed.gz');
        }
    }

    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $filter = new Zend_Filter_Compress_Gz();

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options.
     */
    public function testGzGetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals(['mode' => 'compress', 'level' => 9, 'archive' => null], $filter->getOptions());

        $this->assertEquals(9, $filter->getOptions('level'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(['nooption' => 'foo']);
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(['level' => 6]);
        $this->assertEquals(6, $filter->getOptions('level'));

        $filter->setOptions(['mode' => 'deflate']);
        $this->assertEquals('deflate', $filter->getOptions('mode'));

        $filter->setOptions(['archive' => 'test.txt']);
        $this->assertEquals('test.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Options through constructor.
     */
    public function testGzGetSetOptionsInConstructor()
    {
        $filter2 = new Zend_Filter_Compress_Gz(['level' => 8]);
        $this->assertEquals(['mode' => 'compress', 'level' => 8, 'archive' => null], $filter2->getOptions());
    }

    /**
     * Setting Level.
     */
    public function testGzGetSetLevel()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals(9, $filter->getLevel());
        $filter->setLevel(6);
        $this->assertEquals(6, $filter->getOptions('level'));

        try {
            $filter->setLevel(15);
            $this->fail('Exception expected');
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('must be between', $e->getMessage());
        }
    }

    /**
     * Setting Mode.
     */
    public function testGzGetSetMode()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals('compress', $filter->getMode());
        $filter->setMode('deflate');
        $this->assertEquals('deflate', $filter->getOptions('mode'));

        try {
            $filter->setMode('unknown');
            $this->fail('Exception expected');
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('mode not supported', $e->getMessage());
        }
    }

    /**
     * Setting Archive.
     */
    public function testGzGetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive.
     */
    public function testGzCompressToFile()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $archive = __DIR__ . '/../_files/compressed.gz';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2 = new Zend_Filter_Compress_Gz();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress_Gz();
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * Test deflate.
     */
    public function testGzDeflate()
    {
        $filter = new Zend_Filter_Compress_Gz(['mode' => 'deflate']);

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * testing toString.
     */
    public function testGzToString()
    {
        $filter = new Zend_Filter_Compress_Gz();
        $this->assertEquals('Gz', $filter->toString());
    }
}
