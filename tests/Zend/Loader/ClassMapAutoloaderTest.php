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
require_once 'Zend/Loader/ClassMapAutoloader.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Loader
 */
#[AllowDynamicProperties]
class Zend_Loader_ClassMapAutoloaderTest extends \PHPUnit\Framework\TestCase
{
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite(self::class);
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = [];
        }

        // Store original include_path
        $this->includePath = get_include_path();

        $this->loader = new Zend_Loader_ClassMapAutoloader();
    }

    public function tearDown(): void
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testRegisteringNonExistentAutoloadMapRaisesInvalidArgumentException()
    {
        $dir = __DIR__ . '__foobar__';
        $this->expectException(\Zend_Loader_Exception_InvalidArgumentException::class);
        $this->loader->registerAutoloadMap($dir);
    }

    public function testValidMapFileNotReturningMapRaisesInvalidArgumentException()
    {
        $this->expectException(\Zend_Loader_Exception_InvalidArgumentException::class);
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/badmap.php');
    }

    public function testAllowsRegisteringArrayAutoloadMapDirectly()
    {
        $map = [
            \Zend_Loader_Exception::class => __DIR__ . '/../../../library/Zend/Loader/Exception.php',
        ];
        $this->loader->registerAutoloadMap($map);
        $test = $this->loader->getAutoloadMap();
        $this->assertSame($map, $test);
    }

    public function testAllowsRegisteringArrayAutoloadMapViaConstructor()
    {
        $map = [
            \Zend_Loader_Exception::class => __DIR__ . '/../../../library/Zend/Loader/Exception.php',
        ];
        $loader = new Zend_Loader_ClassMapAutoloader([$map]);
        $test = $loader->getAutoloadMap();
        $this->assertSame($map, $test);
    }

    public function testRegisteringValidMapFilePopulatesAutoloader()
    {
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');
        $map = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, is_countable($map) ? count($map) : 0);
        // Just to make sure nothing changes after loading the same map again
        // (loadMapFromFile should just return)
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');
        $map = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, is_countable($map) ? count($map) : 0);
    }

    public function testRegisteringMultipleMapsMergesThem()
    {
        $map = [
            \Zend_Loader_Exception::class => __DIR__ . '/../../../library/Zend/Loader/Exception.php',
            'Zend_Loader_StandardAutoloaderTest' => 'some/bogus/path.php',
        ];
        $this->loader->registerAutoloadMap($map);
        $this->loader->registerAutoloadMap(__DIR__ . '/_files/goodmap.php');

        $test = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, is_countable($test) ? count($test) : 0);
        $this->assertNotEquals($map['Zend_Loader_StandardAutoloaderTest'], $test['Zend_Loader_StandardAutoloaderTest']);
    }

    public function testCanRegisterMultipleMapsAtOnce()
    {
        $map = [
            \Zend_Loader_Exception::class => __DIR__ . '/../../../library/Zend/Loader/Exception.php',
            'Zend_Loader_StandardAutoloaderTest' => 'some/bogus/path.php',
        ];
        $maps = [$map, __DIR__ . '/_files/goodmap.php'];
        $this->loader->registerAutoloadMaps($maps);
        $test = $this->loader->getAutoloadMap();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, is_countable($test) ? count($test) : 0);
    }

    public function testRegisterMapsThrowsExceptionForNonTraversableArguments()
    {
        $tests = [true, 'string', 1, 1.0, new stdClass()];
        foreach ($tests as $test) {
            try {
                $this->loader->registerAutoloadMaps($test);
                $this->fail('Should not register non-traversable arguments');
            } catch (Zend_Loader_Exception_InvalidArgumentException $e) {
                $this->assertStringContainsString('array or implement Traversable', $e->getMessage());
            }
        }
    }

    public function testAutoloadLoadsClasses()
    {
        $map = ['Zend_UnusualNamespace_ClassMappedClass' => __DIR__ . '/TestAsset/ClassMappedClass.php'];
        $this->loader->registerAutoloadMap($map);
        $this->loader->autoload('Zend_UnusualNamespace_ClassMappedClass');
        $this->assertTrue(class_exists('Zend_UnusualNamespace_ClassMappedClass', false));
    }

    public function testIgnoresClassesNotInItsMap()
    {
        $map = ['Zend_UnusualNamespace_ClassMappedClass' => __DIR__ . '/TestAsset/ClassMappedClass.php'];
        $this->loader->registerAutoloadMap($map);
        $this->loader->autoload('Zend_UnusualNamespace_UnMappedClass');
        $this->assertFalse(class_exists('Zend_UnusualNamespace_UnMappedClass', false));
    }

    public function testRegisterRegistersCallbackWithSplAutoload()
    {
        $this->loader->register();
        $loaders = spl_autoload_functions();
        $this->assertTrue((is_countable($this->loaders) ? count($this->loaders) : 0) < (is_countable($loaders) ? count($loaders) : 0));
        $found = false;
        foreach ($loaders as $loader) {
            if ($loader == [$this->loader, 'autoload']) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Autoloader not found in stack');
    }

    public function testCanLoadClassMapFromPhar()
    {
        if (!class_exists(\Phar::class)) {
            $this->markTestSkipped('Test requires Phar extension');
        }
        $map = 'phar://' . __DIR__ . '/_files/classmap.phar/test/.//../autoload_classmap.php';
        $this->loader->registerAutoloadMap($map);
        $this->loader->autoload('some_loadedclass');
        $this->assertTrue(class_exists('some_loadedclass', false));
        $test = $this->loader->getAutoloadMap();
        $this->assertEquals(2, is_countable($test) ? count($test) : 0);

        // will not register duplicate, even with a different relative path
        $map = 'phar://' . __DIR__ . '/_files/classmap.phar/test/./foo/../../autoload_classmap.php';
        $this->loader->registerAutoloadMap($map);
        $test = $this->loader->getAutoloadMap();
        $this->assertEquals(2, is_countable($test) ? count($test) : 0);
    }

    public function testCanLoadNamespacedClassFromPhar()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped('Namespace support is valid for PHP >= 5.3.0 only');
        }

        $map = 'phar://' . __DIR__ . '/_files/classmap.phar/test/.//../autoload_classmap.php';
        $this->loader->registerAutoloadMap($map);
        $this->loader->autoload('some\namespacedclass');
        $this->assertTrue(class_exists('some\namespacedclass', false));
    }
}
