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
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

require_once 'Zend/Controller/Plugin/ErrorHandler.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Layout
 */
#[AllowDynamicProperties]
class Zend_Layout_FunctionalTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite(self::class);
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $this->bootstrap = [$this, 'appBootstrap'];
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->frontController->setControllerDirectory(__DIR__ . '/_files/functional-test-app/controllers/');

        // create an instance of the ErrorHandler so we can make sure it will point to our specially named ErrorController
        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerController('zend-layout-functional-test-error')
            ->setErrorHandlerAction('error');
        $this->frontController->registerPlugin($plugin, 100);

        Zend_Layout::startMvc(__DIR__ . '/_files/functional-test-app/layouts/');
    }

    public function testMissingViewScriptDoesNotDoubleRender()
    {
        // go to the test controller for this funcitonal test
        $this->dispatch('/zend-layout-functional-test-test/missing-view-script');
        $this->assertEquals(trim($this->response->getBody()), "[DEFAULT_LAYOUT_START]\n(ErrorController::errorAction output)[DEFAULT_LAYOUT_END]");
    }

    public function testMissingViewScriptDoesDoubleRender()
    {
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-91, new Zend_Controller_Action_Helper_ViewRenderer());
        // go to the test controller for this funcitonal test
        $this->dispatch('/zend-layout-functional-test-test/missing-view-script');
        $this->assertEquals(trim($this->response->getBody()), "[DEFAULT_LAYOUT_START]\n[DEFAULT_LAYOUT_START]\n[DEFAULT_LAYOUT_END]\n(ErrorController::errorAction output)[DEFAULT_LAYOUT_END]");
    }
}
