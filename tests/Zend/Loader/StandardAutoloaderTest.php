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
require_once 'Zend/Loader/TestAsset/StandardAutoloader.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Loader
 */
#[AllowDynamicProperties]
class Zend_Loader_StandardAutoloaderTest extends \PHPUnit\Framework\TestCase
{
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

    public function testFallbackAutoloaderFlagDefaultsToFalse()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $this->assertFalse($loader->isFallbackAutoloader());
    }

    public function testFallbackAutoloaderStateIsMutable()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        $this->assertTrue($loader->isFallbackAutoloader());
        $loader->setFallbackAutoloader(false);
        $this->assertFalse($loader->isFallbackAutoloader());
    }

    public function testPassingNonTraversableOptionsToSetOptionsRaisesException()
    {
        $loader = new Zend_Loader_StandardAutoloader();

        $obj = new stdClass();
        foreach ([true, 'foo', $obj] as $arg) {
            try {
                $loader->setOptions(true);
                $this->fail('Setting options with invalid type should fail');
            } catch (Zend_Loader_Exception_InvalidArgumentException $e) {
                $this->assertStringContainsString('array or Traversable', $e->getMessage());
            }
        }
    }

    public function testPassingArrayOptionsPopulatesProperties()
    {
        $options = [
            'namespaces' => [
                'Zend\\' => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR,
            ],
            'prefixes' => [
                'Zend_' => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR,
            ],
            'fallback_autoloader' => true,
        ];
        $loader = new Zend_Loader_TestAsset_StandardAutoloader();
        $loader->setOptions($options);
        $this->assertEquals($options['namespaces'], $loader->getNamespaces());
        $this->assertEquals($options['prefixes'], $loader->getPrefixes());
        $this->assertTrue($loader->isFallbackAutoloader());
    }

    public function testPassingTraversableOptionsPopulatesProperties()
    {
        $namespaces = new ArrayObject([
            'Zend\\' => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR,
        ]);
        $prefixes = new ArrayObject([
            'Zend_' => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR,
        ]);
        $options = new ArrayObject([
            'namespaces' => $namespaces,
            'prefixes' => $prefixes,
            'fallback_autoloader' => true,
        ]);
        $loader = new Zend_Loader_TestAsset_StandardAutoloader();
        $loader->setOptions($options);
        $this->assertEquals((array) $options['namespaces'], $loader->getNamespaces());
        $this->assertEquals((array) $options['prefixes'], $loader->getPrefixes());
        $this->assertTrue($loader->isFallbackAutoloader());
    }

    public function testAutoloadsNamespacedClasses()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped();
        }
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->registerNamespace('Zend\UnusualNamespace', __DIR__ . '/TestAsset');
        $loader->autoload('Zend\UnusualNamespace\NamespacedClass');
        $this->assertTrue(class_exists('Zend\UnusualNamespace\NamespacedClass', false));
    }

    public function testAutoloadsVendorPrefixedClasses()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->registerPrefix('ZendTest_UnusualPrefix', __DIR__ . '/TestAsset/UnusualPrefix');
        $loader->autoload('ZendTest_UnusualPrefix_PrefixedClass');
        $this->assertTrue(class_exists('ZendTest_UnusualPrefix_PrefixedClass', false));
    }

    public function testCanActAsFallbackAutoloader()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        set_include_path(__DIR__ . '/TestAsset/' . PATH_SEPARATOR . $this->includePath);
        $loader->autoload('TestPrefix_FallbackCase');
        $this->assertTrue(class_exists('TestPrefix_FallbackCase', false));
    }

    public function testReturnsFalseForUnresolveableClassNames()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $this->assertFalse($loader->autoload('Some\Fake\Classname'));
    }

    public function testReturnsFalseForInvalidClassNames()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        $this->assertFalse($loader->autoload('Some_Invalid_Classname_'));
    }

    public function testRegisterRegistersCallbackWithSplAutoload()
    {
        $loader = new Zend_Loader_StandardAutoloader();
        $loader->register();
        $loaders = spl_autoload_functions();
        $this->assertTrue((is_countable($this->loaders) ? count($this->loaders) : 0) < (is_countable($loaders) ? count($loaders) : 0));
        $test = array_pop($loaders);
        $this->assertEquals([$loader, 'autoload'], $test);
    }

    public function testAutoloadsNamespacedClassesWithUnderscores()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped('Test only relevant for PHP >= 5.3.0');
        }

        $loader = new Zend_Loader_StandardAutoloader();
        $loader->registerNamespace('ZendTest\UnusualNamespace', __DIR__ . '/TestAsset');
        $loader->autoload('ZendTest\UnusualNamespace\Name_Space\Namespaced_Class');
        $this->assertTrue(class_exists('ZendTest\UnusualNamespace\Name_Space\Namespaced_Class', false));
    }

    public function testZendFrameworkPrefixIsNotLoadedByDefault()
    {
        $loader = new Zend_Loader_TestAsset_StandardAutoloader();
        $expected = [];
        $this->assertSame($expected, $loader->getPrefixes());
    }

    public function testCanTellAutoloaderToRegisterZfPrefixAtInstantiation()
    {
        $loader = new Zend_Loader_TestAsset_StandardAutoloader(['autoregister_zf' => true]);
        $expected = ['Zend_' => dirname(__DIR__, 3) . '/library/Zend/'];
        $this->assertSame($expected, $loader->getPrefixes());
    }
}
