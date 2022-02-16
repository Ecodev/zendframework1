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
 * @see Zend_Filter_Compress
 */
require_once 'Zend/Filter/Compress.php';

/**
 * @group      Zend_Filter
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_CompressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Filter_CompressTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This filter is tested with the bz2 extension');
        }
    }

    public function tearDown(): void
    {
        if (file_exists(__DIR__ . '/../_files/compressed.bz2')) {
            unlink(__DIR__ . '/../_files/compressed.bz2');
        }
    }

    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $filter = new Zend_Filter_Compress('bz2');

        $text = 'compress me';
        $compressed = $filter->filter($text);
        $this->assertNotEquals($text, $compressed);

        $decompressed = $filter->decompress($compressed);
        $this->assertEquals($text, $decompressed);
    }

    /**
     * Setting Options.
     */
    public function testGetSetAdapterOptionsInConstructor()
    {
        $filter = new Zend_Filter_Compress(array(
            'adapter' => 'bz2',
            'options' => array(
                'blocksize' => 6,
                'archive' => 'test.txt',
            ),
        ));

        $this->assertEquals(
            array('blocksize' => 6, 'archive' => 'test.txt'),
            $filter->getAdapterOptions()
        );

        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Options through constructor.
     */
    public function testGetSetAdapterOptions()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $filter->setAdapterOptions(array(
            'blocksize' => 6,
            'archive' => 'test.txt',
        ));
        $this->assertEquals(
            array('blocksize' => 6, 'archive' => 'test.txt'),
            $filter->getAdapterOptions()
        );
        $adapter = $filter->getAdapter();
        $this->assertEquals(6, $adapter->getBlocksize());
        $this->assertEquals('test.txt', $adapter->getArchive());
    }

    /**
     * Setting Blocksize.
     */
    public function testGetSetBlocksize()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals(4, $filter->getBlocksize());
        $filter->setBlocksize(6);
        $this->assertEquals(6, $filter->getOptions('blocksize'));

        try {
            $filter->setBlocksize(15);
            $this->fail('Exception expected');
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('must be between', $e->getMessage());
        }
    }

    /**
     * Setting Archive.
     */
    public function testGetSetArchive()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive.
     */
    public function testCompressToFile()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->filter('compress me');
        $this->assertTrue($content);

        $filter2 = new Zend_Filter_Compress('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress('bz2');
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString.
     */
    public function testToString()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * testing getAdapter.
     */
    public function testGetAdapter()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $adapter = $filter->getAdapter();
        $this->assertTrue($adapter instanceof Zend_Filter_Compress_CompressInterface);
        $this->assertEquals('Bz2', $filter->getAdapterName());
    }

    /**
     * Setting Adapter.
     */
    public function testSetAdapter()
    {
        if (!extension_loaded('zlib')) {
            $this->markTestSkipped('This filter is tested with the zlib extension');
        }

        $filter = new Zend_Filter_Compress();
        $this->assertEquals('Gz', $filter->getAdapterName());

        try {
            $filter->setAdapter(\Zend_Filter::class);
            $adapter = $filter->getAdapter();
            $this->fail('Invalid adapter should fail when retrieved');
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('does not implement', $e->getMessage());
        }
    }

    /**
     * Decompress archiv.
     */
    public function testDecompressArchive()
    {
        $filter = new Zend_Filter_Compress('bz2');
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->filter('compress me');
        $this->assertTrue($content);

        $filter2 = new Zend_Filter_Compress('bz2');
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }

    /**
     * Setting invalid method.
     */
    public function testInvalidMethod()
    {
        $filter = new Zend_Filter_Compress();

        try {
            $filter->invalidMethod();
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Unknown method', $e->getMessage());
        }
    }
}
