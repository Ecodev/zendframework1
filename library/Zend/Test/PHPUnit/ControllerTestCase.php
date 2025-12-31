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
/** @see Zend_Controller_Front */
/** @see Zend_Controller_Action_HelperBroker */
/** @see Zend_Layout */
/** @see Zend_Session */
/** @see Zend_Registry */
/**
 * Functional testing scaffold for MVC applications.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\UsesClass('\PHPUnit\Framework\TestCase')]
abstract class Zend_Test_PHPUnit_ControllerTestCase extends PHPUnit\Framework\TestCase
{
    /**
     * @var mixed Bootstrap file path or callback
     */
    public $bootstrap;

    /**
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * XPath namespaces.
     *
     * @var array
     */
    protected $_xpathNamespaces = [];

    /**
     * Overloading: prevent overloading to special properties.
     *
     * @param  string $name
     * @param  mixed  $value
     */
    public function __set($name, $value)
    {
        if (in_array($name, ['request', 'response', 'frontController'])) {
            throw new Zend_Exception(sprintf('Setting %s object manually is not allowed', $name));
        }
        $this->$name = $value;
    }

    /**
     * Overloading for common properties.
     *
     * Provides overloading for request, response, and frontController objects.
     *
     * @param  mixed $name
     *
     * @return null|Zend_Controller_Front|Zend_Controller_Request_HttpTestCase|Zend_Controller_Response_HttpTestCase
     */
    public function __get($name)
    {
        switch ($name) {
            case 'request':
                return $this->getRequest();
            case 'response':
                return $this->getResponse();
            case 'frontController':
                return $this->getFrontController();
        }

        return null;
    }

    /**
     * Set up MVC app.
     *
     * Calls {@link bootstrap()} by default
     */
    protected function setUp(): void
    {
        $this->bootstrap();
    }

    /**
     * Bootstrap the front controller.
     *
     * Resets the front controller, and then bootstraps it.
     *
     * If {@link $bootstrap} is a callback, executes it; if it is a file, it include's
     * it. When done, sets the test case request and response objects into the
     * front controller.
     */
    final public function bootstrap()
    {
        $this->reset();
        if (null !== $this->bootstrap) {
            if ($this->bootstrap instanceof Zend_Application) {
                $this->bootstrap->bootstrap();
                $this->_frontController = $this->bootstrap->getBootstrap()->getResource('frontcontroller');
            } elseif (is_callable($this->bootstrap)) {
                call_user_func($this->bootstrap);
            } elseif (is_string($this->bootstrap)) {
                if (Zend_Loader::isReadable($this->bootstrap)) {
                    include $this->bootstrap;
                }
            }
        }
        $this->frontController
            ->setRequest($this->getRequest())
            ->setResponse($this->getResponse());
    }

    /**
     * Dispatch the MVC.
     *
     * If a URL is provided, sets it as the request URI in the request object.
     * Then sets test case request and response objects in front controller,
     * disables throwing exceptions, and disables returning the response.
     * Finally, dispatches the front controller.
     *
     * @param null|string $url
     */
    public function dispatch($url = null)
    {
        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $controller = $this->getFrontController();
        $this->frontController
            ->setRequest($request)
            ->setResponse($this->getResponse())
            ->throwExceptions(false)
            ->returnResponse(false);

        if ($this->bootstrap instanceof Zend_Application) {
            $this->bootstrap->run();
        } else {
            $this->frontController->dispatch();
        }
    }

    /**
     * Reset MVC state.
     *
     * Creates new request/response objects, resets the front controller
     * instance, and resets the action helper broker.
     *
     * @todo   Need to update Zend_Layout to add a resetInstance() method
     */
    public function reset()
    {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $this->resetRequest();
        $this->resetResponse();
        Zend_Layout::resetMvcInstance();
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->frontController->resetInstance();
        Zend_Session::$_unitTestEnabled = true;
    }

    /**
     * Rest all view placeholders.
     */
    protected function _resetPlaceholders()
    {
        $registry = Zend_Registry::getInstance();
        $remove = [];
        foreach ($registry as $key => $value) {
            if (strstr($key, '_View_')) {
                $remove[] = $key;
            }
        }

        foreach ($remove as $key) {
            unset($registry[$key]);
        }
    }

    /**
     * Reset the request object.
     *
     * Useful for test cases that need to test multiple trips to the server.
     *
     * @return Zend_Test_PHPUnit_ControllerTestCase
     */
    public function resetRequest()
    {
        if ($this->_request instanceof Zend_Controller_Request_HttpTestCase) {
            $this->_request->clearQuery()
                ->clearPost();
        }
        $this->_request = null;

        return $this;
    }

    /**
     * Reset the response object.
     *
     * Useful for test cases that need to test multiple trips to the server.
     *
     * @return Zend_Test_PHPUnit_ControllerTestCase
     */
    public function resetResponse()
    {
        $this->_response = null;
        $this->_resetPlaceholders();

        return $this;
    }

    /**
     * Register XPath namespaces.
     *
     * @param array $xpathNamespaces
     */
    public function registerXpathNamespaces($xpathNamespaces)
    {
        $this->_xpathNamespaces = $xpathNamespaces;
    }

    /**
     * Assert that the last handled request used the given module.
     *
     * @param string $module
     * @param string $message
     */
    public function assertModule($module, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($module != $this->request->getModuleName()) {
            $msg = sprintf('Failed asserting last module used <"%s"> was "%s"',
                $this->request->getModuleName(),
                $module
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given module.
     *
     * @param string $module
     * @param string $message
     */
    public function assertNotModule($module, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($module == $this->request->getModuleName()) {
            $msg = sprintf('Failed asserting last module used was NOT "%s"', $module);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the last handled request used the given controller.
     *
     * @param string $controller
     * @param string $message
     */
    public function assertController($controller, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($controller != $this->request->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used <"%s"> was "%s"',
                $this->request->getControllerName(),
                $controller
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given controller.
     *
     * @param  string $controller
     * @param  string $message
     */
    public function assertNotController($controller, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($controller == $this->request->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used <"%s"> was NOT "%s"',
                $this->request->getControllerName(),
                $controller
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the last handled request used the given action.
     *
     * @param string $action
     * @param string $message
     */
    public function assertAction($action, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($action != $this->request->getActionName()) {
            $msg = sprintf('Failed asserting last action used <"%s"> was "%s"', $this->request->getActionName(), $action);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given action.
     *
     * @param string $action
     * @param string $message
     */
    public function assertNotAction($action, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($action == $this->request->getActionName()) {
            $msg = sprintf('Failed asserting last action used <"%s"> was NOT "%s"', $this->request->getActionName(), $action);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the specified route was used.
     *
     * @param string $route
     * @param string $message
     */
    public function assertRoute($route, $message = '')
    {
        $this->_incrementAssertionCount();
        $router = $this->frontController->getRouter();
        if ($route != $router->getCurrentRouteName()) {
            $msg = sprintf('Failed asserting matched route was "%s", actual route is %s',
                $route,
                $router->getCurrentRouteName()
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Assert that the route matched is NOT as specified.
     *
     * @param string $route
     * @param string $message
     */
    public function assertNotRoute($route, $message = '')
    {
        $this->_incrementAssertionCount();
        $router = $this->frontController->getRouter();
        if ($route == $router->getCurrentRouteName()) {
            $msg = sprintf('Failed asserting route matched was NOT "%s"', $route);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            static::fail($msg);
        }
    }

    /**
     * Retrieve front controller instance.
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = Zend_Controller_Front::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Retrieve test case request object.
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            $this->_request = new Zend_Controller_Request_HttpTestCase();
        }

        return $this->_request;
    }

    /**
     * Retrieve test case response object.
     *
     * @return Zend_Controller_Response_HttpTestCase
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            $this->_response = new Zend_Controller_Response_HttpTestCase();
        }

        return $this->_response;
    }

    /**
     * URL Helper.
     *
     * @param  array  $urlOptions
     * @param  string $name
     * @param  bool   $reset
     * @param  bool   $encode
     *
     * @return string
     */
    public function url($urlOptions = [], $name = null, $reset = false, $encode = true)
    {
        $frontController = $this->getFrontController();
        $router = $frontController->getRouter();
        if (!$router instanceof Zend_Controller_Router_Rewrite) {
            throw new Exception('This url helper utility function only works when the router is of type Zend_Controller_Router_Rewrite');
        }
        if (count($router->getRoutes()) == 0) {
            $router->addDefaultRoutes();
        }

        return $router->assemble($urlOptions, $name, $reset, $encode);
    }

    /**
     * Urlize options.
     *
     * @param  array $urlOptions
     * @param  bool  $actionControllerModuleOnly
     *
     * @return mixed
     */
    public function urlizeOptions($urlOptions, $actionControllerModuleOnly = true)
    {
        $ccToDash = new Zend_Filter_Word_CamelCaseToDash();
        foreach ($urlOptions as $n => $v) {
            if (in_array($n, ['action', 'controller', 'module'])) {
                $urlOptions[$n] = $ccToDash->filter($v);
            }
        }

        return $urlOptions;
    }

    /**
     * Increment assertion count.
     */
    protected function _incrementAssertionCount()
    {
        $stack = debug_backtrace();
        foreach ($stack as $step) {
            if (isset($step['object'])
                && $step['object'] instanceof PHPUnit\Framework\TestCase
            ) {
                $step['object']->addToAssertionCount(1);

                break;
            }
        }
    }
}
