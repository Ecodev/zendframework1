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

// Call Zend_Loader_PluginLoaderTest::main() if this source file is executed directly.

require_once 'Zend/Loader/PluginLoader.php';

/**
 * Test class for Zend_Loader_PluginLoader.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class Zend_Loader_PluginLoaderTest extends \PHPUnit\Framework\TestCase
{
    protected $_includeCache;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Loader_PluginLoaderTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        if ($this->_includeCache && file_exists($this->_includeCache)) {
            unlink($this->_includeCache);
        }
        Zend_Loader_PluginLoader::setIncludeFileCache(null);
        $this->_includeCache = __DIR__ . '/_files/includeCache.inc.php';
        $this->libPath = realpath(__DIR__ . '/../../../library');
        $this->key = null;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        $this->clearStaticPaths();
        Zend_Loader_PluginLoader::setIncludeFileCache(null);
        if (file_exists($this->_includeCache)) {
            unlink($this->_includeCache);
        }
    }

    public function clearStaticPaths()
    {
        if (null !== $this->key) {
            $loader = new Zend_Loader_PluginLoader([], $this->key);
            $loader->clearPaths();
        }
    }

    public function testAddPrefixPathNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, is_countable($paths['Zend_View_']) ? count($paths['Zend_View_']) : 0);
        $this->assertEquals(2, is_countable($paths['Zend_Loader_']) ? count($paths['Zend_Loader_']) : 0);
    }

    public function testAddPrefixPathMultipleTimes()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader');
        $paths = $loader->getPaths();

        $this->assertTrue(is_array($paths));
        $this->assertEquals(1, is_countable($paths['Zend_Loader_']) ? count($paths['Zend_Loader_']) : 0);
    }

    public function testAddPrefixPathStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $this->assertTrue(array_key_exists('Zend_View_', $paths));
        $this->assertTrue(array_key_exists('Zend_Loader_', $paths));
        $this->assertEquals(1, is_countable($paths['Zend_View_']) ? count($paths['Zend_View_']) : 0);
        $this->assertEquals(2, is_countable($paths['Zend_Loader_']) ? count($paths['Zend_Loader_']) : 0);
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPrefix()
    {
        $loader = new Zend_Loader_PluginLoader();

        try {
            $loader->addPrefixPath([], $this->libPath);
            $this->fail('addPrefixPath() should throw exception with non-string prefix');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testAddPrefixPathThrowsExceptionWithNonStringPath()
    {
        $loader = new Zend_Loader_PluginLoader();

        try {
            $loader->addPrefixPath('Foo_Bar', []);
            $this->fail('addPrefixPath() should throw exception with non-string path');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testRemoveAllPathsForGivenPrefixNonStatically()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths(\Zend_Loader::class);
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->removePrefixPath(\Zend_Loader::class);
        $this->assertFalse($loader->getPaths(\Zend_Loader::class));
    }

    public function testRemoveAllPathsForGivenPrefixStatically()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths(\Zend_Loader::class);
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->removePrefixPath(\Zend_Loader::class);
        $this->assertFalse($loader->getPaths(\Zend_Loader::class));
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();

        try {
            $loader->removePrefixPath('Foo_Bar');
            $this->fail('Removing non-existent prefix should throw an exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testRemovePrefixPathThrowsExceptionIfPrefixPathPairNotRegistered()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Bar', realpath(__DIR__));
        $paths = $loader->getPaths();
        $this->assertTrue(isset($paths['Foo_Bar_']));

        try {
            $loader->removePrefixPath('Foo_Bar', $this->libPath);
            $this->fail('Removing non-existent prefix/path pair should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testClearPathsNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, is_countable($paths) ? count($paths) : 0);
    }

    public function testClearPathsStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->clearPaths();
        $paths = $loader->getPaths();
        $this->assertEquals(0, is_countable($paths) ? count($paths) : 0);
    }

    public function testClearPathsWithPrefixNonStaticallyClearsPathArray()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->clearPaths(\Zend_Loader::class);
        $paths = $loader->getPaths();
        $this->assertEquals(1, is_countable($paths) ? count($paths) : 0);
    }

    public function testClearPathsWithPrefixStaticallyClearsPathArray()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath(\Zend_View::class, $this->libPath . '/Zend/View')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend/Loader')
            ->addPrefixPath(\Zend_Loader::class, $this->libPath . '/Zend');
        $paths = $loader->getPaths();
        $this->assertEquals(2, is_countable($paths) ? count($paths) : 0);
        $loader->clearPaths(\Zend_Loader::class);
        $paths = $loader->getPaths();
        $this->assertEquals(1, is_countable($paths) ? count($paths) : 0);
    }

    public function testGetClassNameNonStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testGetClassNameStaticallyReturnsFalseWhenClassNotLoaded()
    {
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $this->assertFalse($loader->getClassName('FormElement'));
    }

    public function testLoadPluginNonStaticallyLoadsClass()
    {
        $className = null;
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('FormButton');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals(\Zend_View_Helper_FormButton::class, $className);
        $this->assertTrue(class_exists(\Zend_View_Helper_FormButton::class, false));
        $this->assertTrue($loader->isLoaded('FormButton'));
    }

    public function testLoadPluginStaticallyLoadsClass()
    {
        $className = null;
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('FormRadio');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals(\Zend_View_Helper_FormRadio::class, $className);
        $this->assertTrue(class_exists(\Zend_View_Helper_FormRadio::class, false));
        $this->assertTrue($loader->isLoaded('FormRadio'));
    }

    public function testLoadThrowsExceptionIfFileFoundInPrefixButClassNotLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('Doctype');
            $this->fail('Invalid prefix for a path should throw an exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testLoadThrowsExceptionIfNoHelperClassLoaded()
    {
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Foo_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('FooBarBazBat');
            $this->fail('Not finding a helper should throw an exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testGetClassAfterNonStaticLoadReturnsResolvedClassName()
    {
        $className = null;
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('FormSelect');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSelect'));
        $this->assertEquals(\Zend_View_Helper_FormSelect::class, $loader->getClassName('FormSelect'));
    }

    public function testGetClassAfterStaticLoadReturnsResolvedClassName()
    {
        $className = null;
        $this->key = 'foobar';
        $loader = new Zend_Loader_PluginLoader([], $this->key);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');

        try {
            $className = $loader->load('FormCheckbox');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormCheckbox'));
        $this->assertEquals(\Zend_View_Helper_FormCheckbox::class, $loader->getClassName('FormCheckbox'));
    }

    public function testClassFilesAreSearchedInLifoOrder()
    {
        $className = null;
        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', __DIR__ . '/_files/ZfTest');

        try {
            $className = $loader->load('FormSubmit');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('FormSubmit'));
        $this->assertEquals('ZfTest_FormSubmit', $loader->getClassName('FormSubmit'));
    }

    /**
     * @group ZF-2741
     */
    public function testWin32UnderscoreSpacedShortNamesWillLoad()
    {
        $className = null;
        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath(\Zend_Filter::class, $this->libPath . '/Zend/Filter');

        try {
            // Plugin loader will attempt to load "c:\path\to\library/Zend/Filter/Word\UnderscoreToDash.php"
            $className = $loader->load('Word_UnderscoreToDash');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals($className, $loader->getClassName('Word_UnderscoreToDash'));
    }

    /**
     * @group ZF-4670
     */
    public function testIncludeCacheShouldBeNullByDefault()
    {
        $this->assertNull(Zend_Loader_PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldAllowSpecifyingIncludeFileCache()
    {
        $cacheFile = $this->_includeCache;
        $this->testIncludeCacheShouldBeNullByDefault();
        Zend_Loader_PluginLoader::setIncludeFileCache($cacheFile);
        $this->assertEquals($cacheFile, Zend_Loader_PluginLoader::getIncludeFileCache());
    }

    /**
     * @group ZF-4670
     */
    public function testPluginLoaderShouldThrowExceptionWhenPathDoesNotExist()
    {
        $this->expectException(\Zend_Loader_PluginLoader_Exception::class);
        $cacheFile = __DIR__ . '/_filesDoNotExist/includeCache.inc.php';
        $this->testIncludeCacheShouldBeNullByDefault();
        Zend_Loader_PluginLoader::setIncludeFileCache($cacheFile);
        $this->fail('Should not allow specifying invalid cache file path');
    }

    /**
     * @group ZF-5208
     */
    public function testStaticRegistryNamePersistsInDifferentLoaderObjects()
    {
        $loader1 = new Zend_Loader_PluginLoader([], 'PluginLoaderStaticNamespace');
        $loader1->addPrefixPath('Zend_View_Helper', 'Zend/View/Helper');

        $loader2 = new Zend_Loader_PluginLoader([], 'PluginLoaderStaticNamespace');
        $this->assertEquals([
            'Zend_View_Helper_' => ['Zend/View/Helper/'],
        ], $loader2->getPaths());
    }

    /**
     * @group ZF-4697
     */
    public function testClassFilesGrabCorrectPathForLoadedClasses()
    {
        require_once 'Zend/View/Helper/DeclareVars.php';
        $reflection = new ReflectionClass(\Zend_View_Helper_DeclareVars::class);
        $expected = $reflection->getFileName();

        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath('Zend_View_Helper', $this->libPath . '/Zend/View/Helper');
        $loader->addPrefixPath('ZfTest', __DIR__ . '/_files/ZfTest');

        try {
            // Class in /Zend/View/Helper and not in /_files/ZfTest
            $className = $loader->load('DeclareVars');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }

        $classPath = $loader->getClassPath('DeclareVars');
        $this->assertStringContainsString($expected, $classPath);
    }

    /**
     * @group ZF-7350
     */
    public function testPrefixesEndingInBackslashDenoteNamespacedClasses()
    {
        $className = null;
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(self::class . '::' . __METHOD__ . ' requires PHP 5.3.0 or greater');

            return;
        }
        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath('Zfns\\', __DIR__ . '/_files/Zfns');

        try {
            $className = $loader->load('Foo');
        } catch (Exception $e) {
            $paths = $loader->getPaths();
            $this->fail(sprintf('Failed loading helper; paths: %s', var_export($paths, 1)));
        }
        $this->assertEquals('Zfns\\Foo', $className);
        $this->assertEquals('Zfns\\Foo', $loader->getClassName('Foo'));
    }

    /**
     * @group ZF-9721
     */
    public function testRemovePrefixPathThrowsExceptionIfPathNotRegisteredInPrefix()
    {
        $loader = null;

        try {
            $loader = new Zend_Loader_PluginLoader(['My_Namespace_' => 'My/Namespace/']);
            $loader->removePrefixPath('My_Namespace_', 'ZF9721');
            $this->fail();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Loader_PluginLoader_Exception);
            $this->assertStringContainsString('Prefix My_Namespace_ / Path ZF9721', $e->getMessage());
        }
        $this->assertEquals(1, is_countable($loader->getPaths('My_Namespace_')) ? count($loader->getPaths('My_Namespace_')) : 0);
    }

    /**
     * @group ZF-11330
     */
    public function testLoadClassesWithBackslashInName()
    {
        $className = null;
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(self::class . '::' . __METHOD__ . ' requires PHP 5.3.0 or greater');

            return;
        }
        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath('Zfns\\', __DIR__ . '/_files/Zfns');

        try {
            $className = $loader->load('Foo\\Bar');
        } catch (Exception $e) {
            $this->fail(sprintf('Failed loading helper with backslashes in name'));
        }
        $this->assertEquals('Zfns\\Foo\\Bar', $className);
    }

    /**
     * @url https://github.com/zendframework/zf1/issues/152
     */
    public function testLoadClassesWithBackslashAndUnderscoreInName()
    {
        $className = null;
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped(self::class . '::' . __METHOD__ . ' requires PHP 5.3.0 or greater');

            return;
        }

        $loader = new Zend_Loader_PluginLoader([]);
        $loader->addPrefixPath('Zfns\\Foo_', __DIR__ . '/_files/Zfns/Foo');

        try {
            $className = $loader->load('Demo');
        } catch (Exception $e) {
            $this->fail(sprintf('Failed loading helper with backslashes and underscores in name'));
        }

        $this->assertEquals('Zfns\Foo_Demo', $className);
    }
}
