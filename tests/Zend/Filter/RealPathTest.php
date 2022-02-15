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
 * @see Zend_Filter_RealPath
 */
require_once 'Zend/Filter/RealPath.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_RealPathTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Path to test files.
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Zend_Filter_Basename object.
     *
     * @var Zend_Filter_Basename
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Basename object for each test method.
     */
    public function setUp()
    {
        $this->_filesPath = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $this->_filter = new Zend_Filter_RealPath();
    }

    /**
     * Ensures expected behavior for existing file.
     */
    public function testFileExists()
    {
        $filename = 'file.1';
        $this->assertContains($filename, $this->_filter->filter($this->_filesPath . DIRECTORY_SEPARATOR . $filename));
    }

    /**
     * Ensures expected behavior for nonexistent file.
     */
    public function testFileNonexistent()
    {
        $path = '/path/to/nonexistent';
        if (false !== strpos(PHP_OS, 'BSD')) {
            $this->assertEquals($path, $this->_filter->filter($path));
        } else {
            $this->assertEquals(false, $this->_filter->filter($path));
        }
    }

    public function testGetAndSetExistsParameter()
    {
        $this->assertTrue($this->_filter->getExists());
        $this->_filter->setExists(false);
        $this->assertFalse($this->_filter->getExists());

        $this->_filter->setExists(true);
        $this->_filter->setExists(array('exists' => false));
        $this->assertFalse($this->_filter->getExists());

        $this->_filter->setExists(array('unknown'));
        $this->assertTrue($this->_filter->getExists());
    }

    public function testNonExistantPath()
    {
        $this->_filter->setExists(false);

        $path = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $this->_filter->filter($path));

        $path2 = __DIR__ . DIRECTORY_SEPARATOR . '_files'
               . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $this->_filter->filter($path2));

        $path3 = __DIR__ . DIRECTORY_SEPARATOR . '_files'
               . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.'
               . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $this->_filter->filter($path3));
    }
}
