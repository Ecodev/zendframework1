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
 * Zend_Loader_Autoloader.
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * Zend_Controller_Front.
 */
require_once 'Zend/Controller/Front.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Application')]
class Zend_Application_Resource_FrontcontrollerTest extends PHPUnit\Framework\TestCase
{
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite(self::class);
        $result = (new PHPUnit\TextUI\TestRunner())->run($suite);
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

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        require_once __DIR__ . '/../_files/ZfAppBootstrap.php';
        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown(): void
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        Zend_Controller_Front::getInstance()->resetInstance();

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testInitializationCreatesFrontControllerInstance()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([]);
        $resource->init();
        $this->assertTrue($resource->getFrontController() instanceof Zend_Controller_Front);
    }

    public function testInitializationPushesFrontControllerToBootstrapWhenPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([]);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertSame($resource->getFrontController(), $this->bootstrap->frontController);
    }

    public function testShouldSetControllerDirectoryWhenStringOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'controllerDirectory' => __DIR__,
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dir = $front->getControllerDirectory('default');
        $this->assertEquals(__DIR__, $dir);
    }

    public function testShouldSetControllerDirectoryWhenArrayOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'controllerDirectory' => [
                'foo' => __DIR__,
            ],
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dir = $front->getControllerDirectory('foo');
        $this->assertEquals(__DIR__, $dir);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-6458')]
    public function testAllControllerDirectoriesShouldBeSetWhenArrayPassedToControllerDirectoryOption()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'controllerDirectory' => [
                'foo' => __DIR__,
                'bar' => __DIR__,
            ],
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dirs = $front->getControllerDirectory();
        $this->assertEquals([
            'foo' => __DIR__,
            'bar' => __DIR__,
        ], $dirs);
    }

    public function testShouldSetModuleControllerDirectoryNameWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'moduleControllerDirectoryName' => 'foo',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dir = $front->getModuleControllerDirectoryName();
        $this->assertEquals('foo', $dir);
    }

    public function testShouldSetModuleDirectoryWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'moduleDirectory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dir = $front->getControllerDirectory();
        $expected = [
            'bar' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'bar' . DIRECTORY_SEPARATOR . 'controllers',
            'default' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'default' . DIRECTORY_SEPARATOR . 'controllers',
            'foo-bar' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo-bar' . DIRECTORY_SEPARATOR . 'controllers',
            'foo' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo' . DIRECTORY_SEPARATOR . 'controllers',
            'baz' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'baz' . DIRECTORY_SEPARATOR . 'controllers',
            'zfappbootstrap' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                              . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                              . 'zfappbootstrap' . DIRECTORY_SEPARATOR . 'controllers',
        ];
        $this->assertEquals($expected, $dir);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-9258')]
    public function testShouldSetMultipleModuleDirectorysWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'moduleDirectory' => [__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules',
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                               . '_files' . DIRECTORY_SEPARATOR . 'more_modules', ],
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $dir = $front->getControllerDirectory();
        $expected = [
            'bar' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'bar' . DIRECTORY_SEPARATOR . 'controllers',
            'default' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'default' . DIRECTORY_SEPARATOR . 'controllers',
            'foo-bar' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo-bar' . DIRECTORY_SEPARATOR . 'controllers',
            'foo' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'foo' . DIRECTORY_SEPARATOR . 'controllers',
            'baz' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                       . 'baz' . DIRECTORY_SEPARATOR . 'controllers',
            'zfappbootstrap' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                              . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
                              . 'zfappbootstrap' . DIRECTORY_SEPARATOR . 'controllers',
            'bat' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'more_modules' . DIRECTORY_SEPARATOR
                       . 'bat' . DIRECTORY_SEPARATOR . 'controllers',
            'foobaz' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                       . '_files' . DIRECTORY_SEPARATOR . 'more_modules' . DIRECTORY_SEPARATOR
                       . 'foobaz' . DIRECTORY_SEPARATOR . 'controllers',
        ];
        $this->assertEquals($expected, $dir);
    }

    public function testShouldSetDefaultControllerNameWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'defaultControllerName' => 'foo',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $test = $front->getDefaultControllerName();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultActionWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'defaultAction' => 'foo',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $test = $front->getDefaultAction();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetDefaultModuleWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'defaultModule' => 'foo',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $test = $front->getDefaultModule();
        $this->assertEquals('foo', $test);
    }

    public function testShouldSetBaseUrlWhenOptionPresent()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'baseUrl' => '/foo',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $test = $front->getBaseUrl();
        $this->assertEquals('/foo', $test);
    }

    public function testShouldSetParamsWhenOptionPresent()
    {
        $params = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];
        $resource = new Zend_Application_Resource_Frontcontroller([
            'params' => $params,
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $test = $front->getParams();
        $this->assertEquals($params, $test);
    }

    public function testShouldInstantiateAndRegisterPluginsWhenOptionPassed()
    {
        $plugins = [
            Zend_Controller_Plugin_ActionStack::class,
        ];
        $resource = new Zend_Application_Resource_Frontcontroller([
            'plugins' => $plugins,
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        foreach ($plugins as $class) {
            $this->assertTrue($front->hasPlugin($class));
        }
    }

    public function testShouldReturnFrontControllerWhenComplete()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'controllerDirectory' => __DIR__,
        ]);
        $front = $resource->init();
        $this->assertTrue($front instanceof Zend_Controller_Front);
    }

    public function testNoBaseUrlShouldBeSetIfEmptyBaseUrlProvidedInOptions()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'baseurl' => '',
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertNull($front->getBaseUrl());
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-9044')]
    public function testSettingOfRegisterPluginIndexActuallyWorks()
    {
        $plugins = [
            ['class' => Zend_Controller_Plugin_ErrorHandler::class,
                'stackindex' => 10, ],
            Zend_Controller_Plugin_ActionStack::class,
            ['class' => Zend_Controller_Plugin_PutHandler::class,
                'stackIndex' => 5, ],
        ];

        $expected = [
            1 => Zend_Controller_Plugin_ActionStack::class,
            5 => Zend_Controller_Plugin_PutHandler::class,
            10 => Zend_Controller_Plugin_ErrorHandler::class,
        ];

        $resource = new Zend_Application_Resource_Frontcontroller([
            'plugins' => $plugins,
        ]);

        $resource->init();
        $front = $resource->getFrontController();
        $plugins = $front->getPlugins();

        $this->assertEquals(count($expected), count($plugins));
        foreach ($expected as $index => $class) {
            $this->assertEquals($class, $plugins[$index]::class);
        }
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-7367')]
    public function testPassingReturnResponseFlagShouldAlterFrontControllerStatus()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'returnresponse' => true,
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertTrue($front->returnResponse());
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-9724')]
    public function testShouldSetDispatcherFromConfiguration()
    {
        $resource = new Zend_Application_Resource_Frontcontroller([
            'dispatcher' => [
                'class' => 'ZF9724_Dispatcher',
                'params' => [
                    'bar' => 'baz',
                ],
            ],
        ]);
        $resource->init();
        $front = $resource->getFrontController();
        $this->assertEquals('ZF9724_Dispatcher', $front->getDispatcher()::class);
        $this->assertEquals('baz', $front->getDispatcher()->getParam('bar'));
    }
}

class ZF9724_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
}
