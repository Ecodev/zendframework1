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

/** Zend_View_Helper_Action */
require_once 'Zend/View/Helper/Action.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';

/** Zend_Controller_Response_Http */
require_once 'Zend/Controller/Response/Http.php';

/** Zend_View */
require_once 'Zend/View.php';

/**
 * Test class for Zend_View_Helper_Action.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_ActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_ActionTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->_origServer = $_SERVER;
        $_SERVER = [
            'SCRIPT_FILENAME' => __FILE__,
            'PHP_SELF' => __FILE__,
        ];

        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();

        $this->request = new Zend_Controller_Request_Http('http://framework.zend.com/action-foo');
        $this->response = new Zend_Controller_Response_Http();
        $this->response->headersSentThrowsException = false;
        $front->setRequest($this->request)
            ->setResponse($this->response)
            ->addModuleDirectory(__DIR__ . '/_files/modules');

        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_Action();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->request, $this->response, $this->helper);
        $_SERVER = $this->_origServer;
    }

    public function testInitialStateHasClonedObjects()
    {
        $this->assertNotSame($this->request, $this->helper->request);
        $this->assertNotSame($this->response, $this->helper->response);

        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $this->assertNotSame($dispatcher, $this->helper->dispatcher);
    }

    public function testInitialStateHasDefaultModuleName()
    {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $module = $dispatcher->getDefaultModule();
        $this->assertEquals($module, $this->helper->defaultModule);

        $dispatcher->setDefaultModule('foo');
        $helper = new Zend_View_Helper_Action();
        $this->assertEquals('foo', $helper->defaultModule);
    }

    public function testResetObjectsClearsRequestVars()
    {
        $this->helper->request->setParam('foo', 'action-bar');
        $this->helper->resetObjects();
        $this->assertNull($this->helper->request->getParam('foo'));
    }

    public function testResetObjectsClearsResponseBody()
    {
        $this->helper->response->setBody('foobarbaz');
        $this->helper->resetObjects();
        $body = $this->helper->response->getBody();
        $this->assertTrue(empty($body));
    }

    public function testResetObjectsClearsResponseHeaders()
    {
        $this->helper->response->setHeader('X-Foo', 'Bar')
            ->setRawHeader('HTTP/1.1');
        $this->helper->resetObjects();
        $headers = $this->helper->response->getHeaders();
        $rawHeaders = $this->helper->response->getRawHeaders();
        $this->assertTrue(empty($headers));
        $this->assertTrue(empty($rawHeaders));
    }

    public function testActionReturnsContentFromDefaultModule()
    {
        $value = $this->helper->action('bar', 'action-foo');
        $this->assertStringContainsString('In default module, FooController::barAction()', $value);
    }

    public function testActionReturnsContentFromSpecifiedModule()
    {
        $value = $this->helper->action('bar', 'foo', 'foo');
        $this->assertStringContainsString('In foo module, Foo_FooController::barAction()', $value);
    }

    public function testActionReturnsContentReflectingPassedParams()
    {
        $value = $this->helper->action('baz', 'action-foo', null, ['bat' => 'This is my message']);
        $this->assertStringNotContainsString('BOGUS', $value, var_export($this->helper->request->getUserParams(), 1));
        $this->assertStringContainsString('This is my message', $value);
    }

    public function testActionReturnsEmptyStringWhenForwardDetected()
    {
        $value = $this->helper->action('forward', 'action-foo');
        $this->assertEquals('', $value);
    }

    public function testActionReturnsEmptyStringWhenRedirectDetected()
    {
        $value = $this->helper->action('redirect', 'action-foo');
        $this->assertEquals('', $value);
    }

    public function testConstructorThrowsExceptionWithNoControllerDirsInFrontController()
    {
        Zend_Controller_Front::getInstance()->resetInstance();

        try {
            $helper = new Zend_View_Helper_Action();
            $this->fail('Empty front controller should cause action helper to throw exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testConstructorThrowsExceptionWithNoRequestInFrontController()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();

        $response = new Zend_Controller_Response_Http();
        $response->headersSentThrowsException = false;
        $front->setResponse($response)
            ->addModuleDirectory(__DIR__ . '/_files/modules');

        try {
            $helper = new Zend_View_Helper_Action();
            $this->fail('No request in front controller should cause action helper to throw exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testConstructorThrowsExceptionWithNoResponseInFrontController()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();

        $request = new Zend_Controller_Request_Http('http://framework.zend.com/foo');
        $front->setRequest($this->request)
            ->addModuleDirectory(__DIR__ . '/_files/modules');

        try {
            $helper = new Zend_View_Helper_Action();
            $this->fail('No response in front controller should cause action helper to throw exception');
        } catch (Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testViewObjectRemainsUnchangedAfterAction()
    {
        $value = $this->helper->action('bar', 'foo', 'foo');
        $this->assertStringContainsString('In foo module, Foo_FooController::barAction()', $value);
        $this->assertNull($this->view->bar);
    }

    public function testNestingActionsDoesNotBreakPlaceholderHelpers()
    {
        $html = $this->helper->action('nest', 'foo', 'foo');
        $title = $this->view->headTitle()->toString();
        $this->assertStringContainsString(' - ', $title, $title);
        $this->assertStringContainsString('Foo Nest', $title);
        $this->assertStringContainsString('Nested Stuff', $title);
    }

    /**
     * @group ZF-2716
     */
    public function testActionWithPartialsUseOfViewRendererReturnsToOriginatingViewState()
    {
        require_once 'Zend/View/Helper/Partial.php';
        $partial = new Zend_View_Helper_Partial();
        $this->view->setScriptPath(__DIR__ . '/_files/modules/default/views/scripts/');
        $partial->setView($this->view);

        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view = $this->view;

        $partial->partial('partialActionCall.phtml');

        $this->assertSame($this->view, Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view);
    }

    /**
     * Future ViewRenderer State issues should be included in this test.
     *
     * @group ZF-2846
     */
    public function testActionReturnsViewRendererToOriginalState()
    {
        // Setup the VR as if we were inside an action controller
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->init();
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        // make sure noRender is false
        $this->assertFalse($viewRenderer->getNoRender());

        $value = $this->helper->action('bar', 'action-foo');

        $viewRendererPostAction = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        // ViewRenderer noRender should still be false
        $this->assertFalse($viewRendererPostAction->getNoRender());
        $this->assertSame($viewRenderer, $viewRendererPostAction);
    }

    /**
     * Multiple call state issue.
     *
     * @group ZF-3456
     */
    public function testActionCalledWithinActionResetsResponseState()
    {
        $value = $this->helper->action('bar-one', 'baz', 'foo');
        $this->assertMatchesRegularExpression('/Baz-Three-View-Script\s+Baz-Two-View-Script\s+Baz-One-View-Script/s', $value);
    }
}
