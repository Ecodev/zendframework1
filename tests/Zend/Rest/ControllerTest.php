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

/** Zend_Rest_Controller */
require_once 'Zend/Rest/Controller.php';

/** Zend_Controller_Request_HttpTestCase */
require_once 'Zend/Controller/Request/HttpTestCase.php';

/** Zend_Controller_Response_HttpTestCase */
require_once 'Zend/Controller/Response/HttpTestCase.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Rest_TestController extends Zend_Rest_Controller
{
    public $testValue = '';

    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = [])
    {
        $this->testValue = '';
    }

    public function indexAction()
    {
        $this->testValue = 'indexAction';
    }

    public function getAction()
    {
        $this->testValue = 'getAction';
    }

    public function headAction()
    {
        $this->testValue = 'headAction';
    }

    public function postAction()
    {
        $this->testValue = 'postAction';
    }

    public function putAction()
    {
        $this->testValue = 'putAction';
    }

    public function deleteAction()
    {
        $this->testValue = 'deleteAction';
    }
}
/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Rest
 */
#[AllowDynamicProperties]
class Zend_Rest_ControllerTest extends \PHPUnit\Framework\TestCase
{
    protected $_testController;

    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Rest_ControllerTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $request = new Zend_Controller_Request_HttpTestCase();
        $response = new Zend_Controller_Response_HttpTestCase();
        $this->_testController = new Zend_Rest_TestController($request, $response);
    }

    public function test_action_methods()
    {
        $this->_testController->indexAction();
        $this->assertEquals('indexAction', $this->_testController->testValue);
        $this->_testController->getAction();
        $this->assertEquals('getAction', $this->_testController->testValue);
        $this->_testController->headAction();
        $this->assertEquals('headAction', $this->_testController->testValue);
        $this->_testController->postAction();
        $this->assertEquals('postAction', $this->_testController->testValue);
        $this->_testController->putAction();
        $this->assertEquals('putAction', $this->_testController->testValue);
        $this->_testController->deleteAction();
        $this->assertEquals('deleteAction', $this->_testController->testValue);
    }
}
