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
 * @see Zend_Filter_File_Rename
 */
require_once 'Zend/Filter/File/Rename.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Filter
 */
#[AllowDynamicProperties]
class Zend_Filter_File_RenameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Path to test files.
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Original testfile.
     *
     * @var string
     */
    protected $_origFile;

    /**
     * Testfile.
     *
     * @var string
     */
    protected $_oldFile;

    /**
     * Testfile.
     *
     * @var string
     */
    protected $_newFile;

    /**
     * Testdirectory.
     *
     * @var string
     */
    protected $_newDir;

    /**
     * Testfile in Testdirectory.
     *
     * @var string
     */
    protected $_newDirFile;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Filter_File_RenameTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets the path to test files.
     */
    public function setUp(): void
    {
        $this->_filesPath = __DIR__ . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_origFile = $this->_filesPath . 'original.file';
        $this->_oldFile = $this->_filesPath . 'testfile.txt';
        $this->_newFile = $this->_filesPath . 'newfile.xml';
        $this->_newDir = $this->_filesPath . DIRECTORY_SEPARATOR . '_testDir2';
        $this->_newDirFile = $this->_newDir . DIRECTORY_SEPARATOR . 'testfile.txt';

        if (file_exists($this->_origFile)) {
            unlink($this->_origFile);
        }

        if (file_exists($this->_newFile)) {
            unlink($this->_newFile);
        }

        if (file_exists($this->_newDirFile)) {
            unlink($this->_newDirFile);
        }

        copy($this->_oldFile, $this->_origFile);
    }

    /**
     * Sets the path to test files.
     */
    public function tearDown(): void
    {
        if (!file_exists($this->_oldFile)) {
            copy($this->_origFile, $this->_oldFile);
        }

        if (file_exists($this->_origFile)) {
            unlink($this->_origFile);
        }

        if (file_exists($this->_newFile)) {
            unlink($this->_newFile);
        }

        if (file_exists($this->_newDirFile)) {
            unlink($this->_newDirFile);
        }
    }

    /**
     * Test single parameter filter.
     */
    public function testConstructSingleValue()
    {
        $filter = new Zend_Filter_File_Rename($this->_newFile);

        $this->assertEquals([0 => ['source' => '*',
            'target' => $this->_newFile,
            'overwrite' => false, ]],
            $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter.
     */
    public function testConstructSingleArray()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newFile, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test full array parameter filter.
     */
    public function testConstructFullOptionsArray()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true,
            'unknown' => false, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter.
     */
    public function testConstructDoubleArray()
    {
        $filter = new Zend_Filter_File_Rename([
            0 => [
                'source' => $this->_oldFile,
                'target' => $this->_newFile, ], ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter.
     */
    public function testConstructTruncatedTarget()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => '*',
            'overwrite' => false, ]], $filter->getFile());

        $this->assertEquals($this->_oldFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter.
     */
    public function testConstructTruncatedSource()
    {
        $filter = new Zend_Filter_File_Rename([
            'target' => $this->_newFile, ]);

        $this->assertEquals([0 => ['source' => '*',
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());

        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single parameter filter by using directory only.
     */
    public function testConstructSingleDirectory()
    {
        $filter = new Zend_Filter_File_Rename($this->_newDir);

        $this->assertEquals([0 => ['source' => '*',
            'target' => $this->_newDir,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only.
     */
    public function testConstructSingleArrayDirectory()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newDir, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newDir,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only.
     */
    public function testConstructDoubleArrayDirectory()
    {
        $filter = new Zend_Filter_File_Rename([
            0 => [
                'source' => $this->_oldFile,
                'target' => $this->_newDir, ], ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newDir,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only.
     */
    public function testConstructTruncatedSourceDirectory()
    {
        $filter = new Zend_Filter_File_Rename([
            'target' => $this->_newDir, ]);

        $this->assertEquals([0 => ['source' => '*',
            'target' => $this->_newDir,
            'overwrite' => false, ]], $filter->getFile());

        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    public function testAddSameFileAgainAndOverwriteExistingTarget()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newDir, ]);

        $filter->addFile([
            'source' => $this->_oldFile,
            'target' => $this->_newFile, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    public function testGetNewName()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newDir, ]);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newDir,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->getNewName($this->_oldFile));
    }

    public function testGetNewNameExceptionWithExistingFile()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newFile, ]);

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());

        try {
            $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('could not be renamed', $e->getMessage());
        }
    }

    public function testGetNewNameOverwriteWithExistingFile()
    {
        $filter = new Zend_Filter_File_Rename([
            'source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true, ]);

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals([0 => ['source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
    }

    public function testAddFileWithString()
    {
        $filter = new Zend_Filter_File_Rename($this->_oldFile);
        $filter->addFile($this->_newFile);

        $this->assertEquals([0 => ['source' => '*',
            'target' => $this->_newFile,
            'overwrite' => false, ]], $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    public function testAddFileWithInvalidOption()
    {
        $filter = new Zend_Filter_File_Rename($this->_oldFile);

        try {
            $filter->addFile(1234);
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('Invalid options', $e->getMessage());
        }
    }

    public function testInvalidContruction()
    {
        try {
            $filter = new Zend_Filter_File_Rename(1234);
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertStringContainsString('Invalid options', $e->getMessage());
        }
    }
}
