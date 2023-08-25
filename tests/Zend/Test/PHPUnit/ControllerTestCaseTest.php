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

/** Zend_Test_PHPUnit_ControllerTestCase */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Session */
require_once 'Zend/Session.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Test class for Zend_Test_PHPUnit_ControllerTestCase.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 * @group      Zend_Test_PHPUnit
 */
#[AllowDynamicProperties]
class Zend_Test_PHPUnit_ControllerTestCaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Test_PHPUnit_ControllerTestCaseTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $_SESSION = [];
        $this->testCase = new Zend_Test_PHPUnit_ControllerTestCaseTest_Concrete();
        $this->testCase->reset();
        $this->testCase->bootstrap = [$this, 'bootstrap'];
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry['router'])) {
            unset($registry['router']);
        }
        if (isset($registry['dispatcher'])) {
            unset($registry['dispatcher']);
        }
        if (isset($registry['plugin'])) {
            unset($registry['plugin']);
        }
        if (isset($registry['viewRenderer'])) {
            unset($registry['viewRenderer']);
        }
        Zend_Session::$_unitTestEnabled = false;
    }

    public function bootstrap()
    {
    }

    public function testGetFrontControllerShouldReturnFrontController()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
    }

    public function testGetFrontControllerShouldReturnSameFrontControllerObjectOnRepeatedCalls()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
        $test = $this->testCase->getFrontController();
        $this->assertSame($controller, $test);
    }

    public function testGetRequestShouldReturnRequestTestCase()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
    }

    public function testGetRequestShouldReturnSameRequestObjectOnRepeatedCalls()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
        $test = $this->testCase->getRequest();
        $this->assertSame($request, $test);
    }

    public function testGetResponseShouldReturnResponseTestCase()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
    }

    public function testGetResponseShouldReturnSameResponseObjectOnRepeatedCalls()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
        $test = $this->testCase->getResponse();
        $this->assertSame($response, $test);
    }

    public function testOverloadingShouldReturnRequestResponseAndFrontControllerObjects()
    {
        $request = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $frontController = $this->testCase->getFrontController();
        $this->assertSame($request, $this->testCase->request);
        $this->assertSame($response, $this->testCase->response);
        $this->assertSame($frontController, $this->testCase->frontController);
    }

    public function testOverloadingShouldPreventSettingRequestResponseAndFrontControllerObjects()
    {
        try {
            $this->testCase->request = new Zend_Controller_Request_Http();
            $this->fail('Setting request object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertStringContainsString('not allow', $e->getMessage());
        }

        try {
            $this->testCase->response = new Zend_Controller_Response_Http();
            $this->fail('Setting response object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertStringContainsString('not allow', $e->getMessage());
        }

        try {
            $this->testCase->frontController = Zend_Controller_Front::getInstance();
            $this->fail('Setting front controller as public property should raise exception');
        } catch (Exception $e) {
            $this->assertStringContainsString('not allow', $e->getMessage());
        }
    }

    public function testResetShouldResetMvcState()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        $request = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $router = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $controller = $this->testCase->getFrontController();
        $controller->setParam('foo', 'bar')
            ->registerPlugin($plugin)
            ->setRouter($router)
            ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->testCase->reset();
        $test = $controller->getRouter();
        $this->assertNotSame($router, $test);
        $test = $controller->getDispatcher();
        $this->assertNotSame($dispatcher, $test);
        $this->assertFalse($controller->getPlugin(\Zend_Controller_Plugin_ErrorHandler::class));
        $test = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->assertNotSame($viewRenderer, $test);
        $this->assertNull($controller->getRequest());
        $this->assertNull($controller->getResponse());
        $this->assertNotSame($request, $this->testCase->getRequest());
        $this->assertNotSame($response, $this->testCase->getResponse());
    }

    public function testBootstrapShouldSetRequestAndResponseTestCaseObjects()
    {
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $request = $controller->getRequest();
        $response = $controller->getResponse();
        $this->assertSame($this->testCase->getRequest(), $request);
        $this->assertSame($this->testCase->getResponse(), $response);
    }

    public function testBootstrapShouldIncludeBootstrapFileSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = __DIR__ . '/_files/bootstrap.php';
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin(\Zend_Controller_Plugin_ErrorHandler::class));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function testBootstrapShouldInvokeCallbackSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = [$this, 'bootstrapCallback'];
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin(\Zend_Controller_Plugin_ErrorHandler::class));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function bootstrapCallback()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Front.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        require_once 'Zend/Registry.php';
        $router = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('foo', 'bar')
            ->registerPlugin($plugin)
            ->setRouter($router)
            ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        Zend_Registry::set('router', $router);
        Zend_Registry::set('dispatcher', $dispatcher);
        Zend_Registry::set('plugin', $plugin);
        Zend_Registry::set('viewRenderer', $viewRenderer);
    }

    public function testDispatchShouldDispatchSpecifiedUrl()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/bar');
        $request = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $content = $response->getBody();
        $this->assertEquals('zend-test-php-unit-foo', $request->getControllerName(), $content);
        $this->assertEquals('bar', $request->getActionName());
        $this->assertStringContainsString('FooController::barAction', $content, $content);
    }

    public function testModuleAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertModule('default');
        $this->testCase->assertNotModule('zend-test-php-unit-foo');
        self::assertTrue(true);
    }

    public function testModuleAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->testCase->assertModule('zend-test-php-unit-foo');
        $this->testCase->assertNotModule('default');
    }

    public function testControllerAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertController('zend-test-php-unit-foo');
        $this->testCase->assertNotController('baz');
        self::assertTrue(true);
    }

    public function testControllerAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->testCase->assertController('baz');
        $this->testCase->assertNotController('zend-test-php-unit-foo');
    }

    public function testActionAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertAction('baz');
        $this->testCase->assertNotAction('zend-test-php-unit-foo');
        self::assertTrue(true);
    }

    public function testActionAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->testCase->assertAction('foo');
        $this->testCase->assertNotAction('baz');
    }

    public function testRouteAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertRoute('default');
        $this->testCase->assertNotRoute('zend-test-php-unit-foo');
        self::assertTrue(true);
    }

    public function testRouteAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->testCase->assertRoute('foo');
        $this->testCase->assertNotRoute('default');
    }

    public function testResetShouldResetSessionArray()
    {
        $this->assertTrue(empty($_SESSION));
        $_SESSION = ['foo' => 'bar', 'bar' => 'baz'];
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $_SESSION, var_export($_SESSION, 1));
        $this->testCase->reset();
        $this->assertTrue(empty($_SESSION));
    }

    public function testResetShouldUnitTestEnableZendSession()
    {
        $this->testCase->reset();
        $this->assertTrue(Zend_Session::$_unitTestEnabled);
    }

    public function testResetResponseShouldClearResponseObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $response = $this->testCase->getResponse();
        $this->testCase->resetResponse();
        $test = $this->testCase->getResponse();
        $this->assertNotSame($response, $test);
    }

    /**
     * @group ZF-4511
     */
    public function testResetRequestShouldClearRequestObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $request = $this->testCase->getRequest();
        $this->testCase->resetRequest();
        $test = $this->testCase->getRequest();
        $this->assertNotSame($request, $test);
    }

    /**
     * @group ZF-4070
     */
    public function testQueryParametersShouldPersistFollowingDispatch()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
            ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/');

        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-4070
     */
    public function testQueryStringShouldNotOverwritePreviouslySetQueryParameters()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
            ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/?spy=super');

        $this->assertEquals('super', $request->getQuery('spy'), '(post) Failed retrieving spy parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-3979
     */
    public function testSuperGlobalArraysShouldBeClearedDuringSetUp()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
            ->setPost('foo', 'bar')
            ->setCookie('bar', 'baz');

        $this->testCase->setUp();
        $this->assertNull($request->getQuery('mr'), 'Retrieved mr get parameter: ' . var_export($request->getQuery(), 1));
        $this->assertNull($request->getPost('foo'), 'Retrieved foo post parameter: ' . var_export($request->getPost(), 1));
        $this->assertNull($request->getCookie('bar'), 'Retrieved bar cookie parameter: ' . var_export($request->getCookie(), 1));
    }

    /**
     * @group ZF-4511
     */
    public function testResetRequestShouldClearPostAndQueryParameters()
    {
        $this->testCase->getFrontController()->setControllerDirectory(__DIR__ . '/_files/application/controllers');
        $this->testCase->getRequest()->setPost([
            'foo' => 'bar',
        ]);
        $this->testCase->getRequest()->setQuery([
            'bar' => 'baz',
        ]);
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->resetRequest();
        $this->assertTrue(empty($_POST));
        $this->assertTrue(empty($_GET));
    }

    /**
     * @group ZF-7839
     */
    public function testTestCaseShouldAllowUsingApplicationObjectAsBootstrap()
    {
        require_once 'Zend/Application.php';
        $application = new Zend_Application('testing', [
            'resources' => [
                'frontcontroller' => [
                    'controllerDirectory' => __DIR__ . '/_files/application/controllers',
                ],
            ],
        ]);
        $this->testCase->bootstrap = $application;
        $this->testCase->bootstrap();
        $this->assertEquals(
            $application->getBootstrap()->getResource('frontcontroller'),
            $this->testCase->getFrontController()
        );
    }

    /**
     * @group ZF-8193
     */
    public function testWhenApplicationObjectUsedAsBootstrapTestCaseShouldExecuteBootstrapRunMethod()
    {
        require_once 'Zend/Application.php';
        $application = new Zend_Application('testing', [
            'resources' => [
                'frontcontroller' => [
                    'controllerDirectory' => __DIR__ . '/_files/application/controllers',
                ],
            ],
        ]);
        $this->testCase->bootstrap = $application;
        $this->testCase->bootstrap();
        $this->testCase->dispatch('/');
        $front = $application->getBootstrap()->getResource('frontcontroller');
        $boot = $front->getParam('bootstrap');
        $type = is_object($boot)
               ? get_class($boot)
               : gettype($boot);
        $this->assertTrue($boot === $this->testCase->bootstrap->getBootstrap(), $type);
    }

    /**
     * Data provider for testRedirectWorksAsExpectedFromHookMethodsInActionController.
     *
     * @return array
     */
    public function providerRedirectWorksAsExpectedFromHookMethodsInActionController()
    {
        return [
            ['/zend-test-redirect-from-init/baz'],
            ['/zend-test-redirect-from-pre-dispatch/baz'],
        ];
    }

    /**
     * Data provider for testRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin.
     *
     * @return array
     */
    public function providerRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin()
    {
        return [
            ['RouteStartup'],
            ['RouteShutdown'],
            ['DispatchLoopStartup'],
            ['PreDispatch'],
        ];
    }
}

// Concrete test case class for testing purposes
#[AllowDynamicProperties]
class Zend_Test_PHPUnit_ControllerTestCaseTest_Concrete extends Zend_Test_PHPUnit_ControllerTestCase
{
}
