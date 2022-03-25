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
 * @see Zend_Filter_Compress_Bz2
 */
require_once 'Zend/Filter/Compress/Bz2.php';

/**
 * @group      Zend_Filter
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_Bz2Test extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs this test suite.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Filter_Compress_Bz2Test');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This adapter needs the bz2 extension');
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
        $filter = new Zend_Filter_Compress_Bz2();

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options.
     */
    public function testBz2GetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $this->assertEquals(['blocksize' => 4, 'archive' => null], $filter->getOptions());

        $this->assertEquals(4, $filter->getOptions('blocksize'));

        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(['blocksize' => 6]);
        $this->assertEquals(6, $filter->getOptions('blocksize'));

        $filter->setOptions(['archive' => 'test.txt']);
        $this->assertEquals('test.txt', $filter->getOptions('archive'));

        $filter->setOptions(['nooption' => 0]);
        $this->assertNull($filter->getOptions('nooption'));
    }

    /**
     * Setting Options through constructor.
     */
    public function testBz2GetSetOptionsInConstructor()
    {
        $filter2 = new Zend_Filter_Compress_Bz2(['blocksize' => 8]);
        $this->assertEquals(['blocksize' => 8, 'archive' => null], $filter2->getOptions());
    }

    /**
     * Setting Blocksize.
     */
    public function testBz2GetSetBlocksize()
    {
        $filter = new Zend_Filter_Compress_Bz2();
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
    public function testBz2GetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive.
     */
    public function testBz2CompressToFile()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2 = new Zend_Filter_Compress_Bz2();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Zend_Filter_Compress_Bz2();
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString.
     */
    public function testBz2ToString()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * Basic usage.
     */
    public function testBz2DecompressArchive()
    {
        $filter = new Zend_Filter_Compress_Bz2();
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2 = new Zend_Filter_Compress_Bz2();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }
}
