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
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_Controller_Plugin_PutHandler.
 */
/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
#[AllowDynamicProperties]
class Zend_Controller_Plugin_PutHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Request object.
     *
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * Error handler plugin.
     *
     * @var Zend_Controller_Plugin_PutHandler
     */
    public $plugin;

    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Controller_Plugin_PutHandlerTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $this->plugin = new Zend_Controller_Plugin_PutHandler();

        $this->plugin->setRequest($this->request);
    }

    public function test_marshall_PUT_body_as_params()
    {
        $this->request->setMethod('PUT');
        $this->request->setRawBody('param1=value1&param2=value2');
        $this->plugin->preDispatch($this->request);

        $this->assertEquals('value1', $this->request->getParam('param1'));
        $this->assertEquals('value2', $this->request->getParam('param2'));
    }
}
