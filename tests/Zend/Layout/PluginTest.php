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
require_once 'Zend/Controller/Response/Cli.php';

/**
 * Test class for Zend_Layout_Controller_Plugin_Layout.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Layout
 */
#[AllowDynamicProperties]
class Zend_Layout_PluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Layout_PluginTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        Zend_Controller_Front::getInstance()->resetInstance();

        Zend_Layout_PluginTest_Layout::resetMvcInstance();

        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        Zend_Layout::resetMvcInstance();
    }

    public function testConstructorWithLayoutObject()
    {
        $layout = new Zend_Layout(['mvcEnabled' => false]);
        $plugin = new Zend_Layout_Controller_Plugin_Layout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsNullWithNoLayoutPresent()
    {
        $plugin = new Zend_Layout_Controller_Plugin_Layout();
        $this->assertNull($plugin->getLayout());
    }

    public function testLayoutAccessorsWork()
    {
        $plugin = new Zend_Layout_Controller_Plugin_Layout();
        $this->assertNull($plugin->getLayout());

        $layout = new Zend_Layout(['mvcEnabled' => false]);
        $plugin->setlayout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsLayoutObjectWhenPulledFromPluginBroker()
    {
        $layout = Zend_Layout::startMvc();
        $front = Zend_Controller_Front::getInstance();
        $this->assertTrue($front->hasPlugin(\Zend_Layout_Controller_Plugin_Layout::class));
        $plugin = $front->getPlugin(\Zend_Layout_Controller_Plugin_Layout::class);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testPostDispatchRendersLayout()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Simple();
        $response = new Zend_Controller_Response_Cli();

        $request->setDispatched(true);
        $response->setBody('Application content');
        $front->setRequest($request)
            ->setResponse($response);

        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
            ->setLayout('plugin.phtml')
            ->disableInflector();

        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('layout');
        $plugin = $front->getPlugin(\Zend_Layout_Controller_Plugin_Layout::class);
        $plugin->setResponse($response);

        $helper->postDispatch();
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Application content', $body, $body);
        $this->assertStringContainsString('Site Layout', $body, $body);
    }

    public function testPostDispatchDoesNotRenderLayoutWhenForwardDetected()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Simple();
        $response = new Zend_Controller_Response_Cli();

        $request->setDispatched(false);
        $response->setBody('Application content');
        $front->setRequest($request)
            ->setResponse($response);

        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
            ->setLayout('plugin.phtml')
            ->disableInflector();

        $plugin = $front->getPlugin(\Zend_Layout_Controller_Plugin_Layout::class);
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Application content', $body);
        $this->assertStringNotContainsString('Site Layout', $body);
    }

    public function testPostDispatchDoesNotRenderLayoutWhenLayoutDisabled()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Simple();
        $response = new Zend_Controller_Response_Cli();

        $request->setDispatched(true);
        $response->setBody('Application content');
        $front->setRequest($request)
            ->setResponse($response);

        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
            ->setLayout('plugin.phtml')
            ->disableInflector()
            ->disableLayout();

        $plugin = $front->getPlugin(\Zend_Layout_Controller_Plugin_Layout::class);
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Application content', $body);
        $this->assertStringNotContainsString('Site Layout', $body);
    }

    /**
     * @group ZF-8041
     */
    public function testPostDispatchDoesNotRenderLayoutWhenResponseRedirected()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = new Zend_Controller_Request_Simple();
        $response = new Zend_Controller_Response_Cli();

        $request->setDispatched(true);
        $response->setHttpResponseCode(302);
        $response->setBody('Application content');
        $front->setRequest($request)
            ->setResponse($response);

        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
            ->setLayout('plugin.phtml')
            ->setMvcSuccessfulActionOnly(false)
            ->disableInflector();

        $plugin = $front->getPlugin(\Zend_Layout_Controller_Plugin_Layout::class);
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertStringContainsString('Application content', $body);
        $this->assertStringNotContainsString('Site Layout', $body);
    }
}

/**
 * Zend_Layout extension to allow resetting MVC instance.
 */
#[AllowDynamicProperties]
class Zend_Layout_PluginTest_Layout extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}
