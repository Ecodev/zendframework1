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
require_once 'Zend/Controller/Action/Helper/Url.php';

require_once 'Zend/Controller/Request/Http.php';

/**
 * Test class for Zend_Controller_Action_Helper_Url.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
#[AllowDynamicProperties]
class Zend_Controller_Action_Helper_UrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Controller_Action_Helper_UrlTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest(new Zend_Controller_Request_Http());
        $this->helper = new Zend_Controller_Action_Helper_Url();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testSimpleWithAllParamsProducesAppropriateUrl()
    {
        $url = $this->helper->simple('baz', 'bar', 'foo', ['bat' => 'foo', 'ho' => 'hum']);
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testSimpleWithMissingControllerAndModuleProducesAppropriateUrl()
    {
        $request = $this->front->getRequest();
        $request->setModuleName('foo')
            ->setControllerName('bar');
        $url = $this->helper->simple('baz', null, null, ['bat' => 'foo', 'ho' => 'hum']);
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testSimpleWithDefaultModuleProducesUrlWithoutModuleSegment()
    {
        $url = $this->helper->simple('baz', 'bar', 'default', ['bat' => 'foo', 'ho' => 'hum']);
        $this->assertEquals('/bar/baz', substr($url, 0, 8));
    }

    public function testUrlMethodCreatesUrlBasedOnNamedRouteAndPassedParameters()
    {
        $router = $this->front->getRouter();
        $route = new Zend_Controller_Router_Route(
            'foo/:action/:page',
            [
                'module' => 'default',
                'controller' => 'foobar',
                'action' => 'bazbat',
                'page' => 1,
            ]
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->url(['action' => 'bar', 'page' => 3], 'foo');
        $this->assertEquals('/foo/bar/3', $url);
    }

    public function testUrlMethodCreatesUrlBasedOnNamedRouteAndDefaultParameters()
    {
        $router = $this->front->getRouter();
        $route = new Zend_Controller_Router_Route(
            'foo/:action/:page',
            [
                'module' => 'default',
                'controller' => 'foobar',
                'action' => 'bazbat',
                'page' => 1,
            ]
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->url([], 'foo');
        $this->assertEquals('/foo', $url);
    }

    public function testUrlMethodCreatesUrlBasedOnPassedParametersUsingDefaultRouteWhenNoNamedRoutePassed()
    {
        $this->front->getRouter()->addDefaultRoutes();
        $this->front->addModuleDirectory(__DIR__ . '/../../_files/modules');
        $url = $this->helper->url([
            'module' => 'foo',
            'controller' => 'bar',
            'action' => 'baz',
            'bat' => 'foo',
            'ho' => 'hum',
        ]);
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testDirectProxiesToSimple()
    {
        $url = $this->helper->direct('baz', 'bar', 'foo', ['bat' => 'foo', 'ho' => 'hum']);
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    /**
     * @group ZF-2822
     */
    public function testBaseUrlIsAssembledIntoUrl()
    {
        $this->front->setBaseUrl('baseurl');

        $request = $this->front->getRequest();
        $request->setModuleName('module')
            ->setControllerName('controller');

        $url = $this->helper->simple('action', null, null, ['foo' => 'bar']);
        $this->assertEquals('/baseurl/module/controller/action/foo/bar', $url);
    }
}
