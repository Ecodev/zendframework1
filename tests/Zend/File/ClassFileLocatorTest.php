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
 */
require_once 'Zend/File/ClassFileLocator.php';

/**
 * Test class for Zend_File_ClassFileLocator.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_File
 */
#[AllowDynamicProperties]
class Zend_File_ClassFileLocatorTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorThrowsInvalidArgumentExceptionForInvalidStringDirectory()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locator = new Zend_File_ClassFileLocator('__foo__');
    }

    public function testConstructorThrowsInvalidArgumentExceptionForNonDirectoryIteratorArgument()
    {
        $iterator = new ArrayIterator([]);
        $this->expectException(\InvalidArgumentException::class);
        $locator = new Zend_File_ClassFileLocator($iterator);
    }

    public function testIterationShouldReturnOnlyPhpFiles()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        foreach ($locator as $file) {
            $this->assertMatchesRegularExpression('/\.php$/', $file->getFilename());
        }
    }

    public function testIterationShouldReturnOnlyPhpFilesContainingClasses()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            if (preg_match('/locator-should-skip-this\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertFalse($found, 'Found PHP file not containing a class?');
    }

    public function testIterationShouldReturnInterfaces()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            if (preg_match('/LocatorShouldFindThis\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Locator skipped an interface?');
    }

    public function testIterationShouldInjectNamespaceInFoundItems()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            $classes = $file->getClasses();
            foreach ($classes as $class) {
                if (strpos($class, '\\', 1)) {
                    $found = true;
                }
            }
        }
        $this->assertTrue($found);
    }

    public function testIterationShouldInjectClassInFoundItems()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            $classes = $file->getClasses();
            foreach ($classes as $class) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testIterationShouldFindMultipleClassesInMultipleNamespacesInSinglePhpFile()
    {
        if (version_compare(PHP_VERSION, '5.3', 'lt')) {
            $this->markTestSkipped('Test can only be run under 5.3 or later');
        }

        $locator = new Zend_File_ClassFileLocator(__DIR__);
        $foundFirst = false;
        $foundSecond = false;
        $foundThird = false;
        $foundFourth = false;
        foreach ($locator as $file) {
            if (preg_match('/MultipleClassesInMultipleNamespaces\.php$/', $file->getFilename())) {
                $classes = $file->getClasses();
                foreach ($classes as $class) {
                    if ($class === 'ZendTest\File\TestAsset\LocatorShouldFindFirstClass') {
                        $foundFirst = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\LocatorShouldFindSecondClass') {
                        $foundSecond = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\SecondTestNamespace\LocatorShouldFindThirdClass') {
                        $foundThird = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\SecondTestNamespace\LocatorShouldFindFourthClass') {
                        $foundFourth = true;
                    }
                }
            }
        }
        $this->assertTrue($foundFirst);
        $this->assertTrue($foundSecond);
        $this->assertTrue($foundThird);
        $this->assertTrue($foundFourth);
    }
}
