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
require_once dirname(__FILE__, 3) . '/_files/HelperFlashMessengerController.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
#[AllowDynamicProperties]
class Zend_Controller_Action_Helper_FlashMessengerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_Controller_Action
     */
    public $controller;

    /**
     * @var Zend_Controller_Front
     */
    public $front;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    public $helper;

    /**
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * @var Zend_Controller_Response_Cli
     */
    public $response;

    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Controller_Action_Helper_FlashMessengerTest');
        $result = (new \PHPUnit\TextUI\TestRunner())->run($suite);
    }

    public function setUp(): void
    {
        $savePath = ini_get('session.save_path');
        if (strpos($savePath, ';')) {
            $savePath = explode(';', (string) $savePath);
            $savePath = array_pop($savePath);
        }
        if (empty($savePath)) {
            $this->markTestSkipped('Cannot test FlashMessenger due to unavailable session save path');
        }

        if (headers_sent()) {
            $this->markTestSkipped('Cannot test FlashMessenger: cannot start session because headers already sent');
        }
        Zend_Session::start();

        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setControllerDirectory(dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . '_files');
        $this->front->returnResponse(true);
        $this->request = new Zend_Controller_Request_Http();
        $this->request->setControllerName('helper-flash-messenger');
        $this->response = new Zend_Controller_Response_Cli();
        $this->controller = new HelperFlashMessengerController($this->request, $this->response, []);
        $this->helper = new Zend_Controller_Action_Helper_FlashMessenger();
    }

    public function testLoadFlashMessenger(): never
    {
        $this->markTestSkipped();
        $response = $this->front->dispatch($this->request);
        static::assertEquals('Zend_Controller_Action_Helper_FlashMessenger123456', $response->getBody());
    }

    public function testClearMessages(): never
    {
        $this->markTestSkipped();
        $this->helper->addMessage('foo');
        $this->helper->addMessage('bar');
        static::assertTrue($this->helper->hasMessages());
        static::assertEquals(2, count($this->helper));

        $this->helper->clearMessages();
        static::assertFalse($this->helper->hasMessages());
        static::assertEquals(0, count($this->helper));
    }

    public function testDirectProxiesToAddMessage(): never
    {
        $this->markTestSkipped();
        $this->helper->direct('foo');
        static::assertTrue($this->helper->hasMessages());
        static::assertEquals(1, count($this->helper));
    }

    /**
     * @group ZF-1705
     */
    public function testNamespaceChange()
    {
        $this->helper->setNamespace('foobar');
        $this->assertEquals('foobar', $this->helper->getNamespace());
    }

    /**
     * @group ZF-1705
     */
    public function testAddMessageToCustomNamespace()
    {
        $this->helper->addMessage('testmessage', 'foobar');
        $this->assertTrue($this->helper->hasCurrentMessages('foobar'));

        $this->helper->addMessage('testmessage2', 'foobar');
        $this->assertTrue($this->helper->hasCurrentMessages('foobar'));

        $foobarMessages = $this->helper->getCurrentMessages('foobar');
        $this->assertEquals(['testmessage', 'testmessage2'], $foobarMessages);

        // Ensure it didnt' bleed over into default namespace
        $defaultMessages = $this->helper->getCurrentMessages();
        $this->assertTrue(empty($defaultMessages), 'Default namespace not empty');
    }

    /**
     * @group ZF-1705
     */
    public function testRemoveMessageToCustomNamespace()
    {
        // Place a message in foobar and default namespaces
        $this->helper->addMessage('testmessage', 'foobar');
        $this->assertTrue($this->helper->hasCurrentMessages('foobar'));
        $this->helper->addMessage('defaultmessage');
        $this->assertTrue($this->helper->hasCurrentMessages());

        // Erase the foobar namespace
        $this->helper->clearCurrentMessages('foobar');

        // Ensure it cleared the specified namespace
        $foobarMessages = $this->helper->getCurrentMessages('foobar');
        $this->assertTrue(empty($foobarMessages), 'Namespace foobar not empty');

        // Ensure it didnt' clear the default namespace
        $defaultMessages = $this->helper->getCurrentMessages();
        $this->assertEquals(1, count($defaultMessages));
        $this->assertEquals('defaultmessage', array_pop($defaultMessages));
    }

    /**
     * @group ZF-1705
     */
    public function testSimulateCrossRequestMessagePassing()
    {
        $helper = new FlashMessengerControllerActionHelper();
        $helper->addMessage('testmessage', 'foobar');
        $helper->addMessage('defaultmessage');

        // Reset and recreate the helper, essentially faking a subsequent request
        $helper->reset();
        $helper = new FlashMessengerControllerActionHelper();

        // Check the contents
        $this->assertFalse($helper->hasCurrentMessages('foobar'));
        $this->assertFalse($helper->hasCurrentMessages());
        $this->assertTrue($helper->hasMessages('foobar'));
        $this->assertTrue($helper->hasMessages());

        $defaultMessages = $helper->getMessages();
        $this->assertEquals(1, count($defaultMessages));
        $this->assertEquals('defaultmessage', array_pop($defaultMessages));

        $foobarMessages = $helper->getMessages('foobar');
        $this->assertEquals(1, count($foobarMessages));
        $this->assertEquals('testmessage', array_pop($foobarMessages));
    }
}

/**
 * Subclass of FlashMessenger action helper which exposes a reset method
 * to allow faking a second (fresh) request.
 */
#[AllowDynamicProperties]
class FlashMessengerControllerActionHelper extends Zend_Controller_Action_Helper_FlashMessenger
{
    public function getName()
    {
        return 'FlashMessenger';
    }

    public function reset()
    {
        self::$_messages = [];
        self::$_session = null;
        self::$_messageAdded = false;
    }
}
