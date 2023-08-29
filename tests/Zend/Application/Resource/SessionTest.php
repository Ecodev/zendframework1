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
require_once 'Zend/Application/Resource/ResourceAbstract.php';
require_once 'Zend/Application/Resource/Session.php';
require_once 'Zend/Session.php';
require_once 'Zend/Session/SaveHandler/Interface.php';

/**
 * @group      Zend_Application
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Application_Resource_SessionTest extends \PHPUnit\Framework\TestCase
{
    public $resource;

    public function setUp(): void
    {
        $this->resource = new Zend_Application_Resource_Session();
    }

    public function testSetSaveHandler()
    {
        $saveHandler = $this->createMock(\Zend_Session_SaveHandler_Interface::class);

        $this->resource->setSaveHandler($saveHandler);
        $this->assertSame($saveHandler, $this->resource->getSaveHandler());
    }

    public function testSetSaveHandlerString()
    {
        $saveHandler = $this->createMock(\Zend_Session_SaveHandler_Interface::class);
        $saveHandlerClassName = $saveHandler::class;

        $this->resource->setSaveHandler($saveHandlerClassName);

        $this->assertTrue($this->resource->getSaveHandler() instanceof $saveHandlerClassName);
    }

    public function testSetSaveHandlerArray()
    {
        $saveHandler = $this->createMock(\Zend_Session_SaveHandler_Interface::class);
        $saveHandlerClassName = $saveHandler::class;

        $this->resource->setSaveHandler(['class' => $saveHandlerClassName]);

        $this->assertTrue($this->resource->getSaveHandler() instanceof $saveHandlerClassName);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetOptions()
    {
        Zend_Session::setOptions([
            'use_only_cookies' => false,
            'remember_me_seconds' => 3600,
        ]);

        $this->resource->setOptions([
            'use_only_cookies' => true,
            'remember_me_seconds' => 7200,
        ]);

        $this->resource->init();

        $this->assertEquals(1, Zend_Session::getOptions('use_only_cookies'));
        $this->assertEquals(7200, Zend_Session::getOptions('remember_me_seconds'));
    }

    public function testInitSetsSaveHandler()
    {
        Zend_Session::$_unitTestEnabled = true;

        $saveHandler = $this->createMock(\Zend_Session_SaveHandler_Interface::class);

        $this->resource->setSaveHandler($saveHandler);

        $this->resource->init();

        $this->assertSame($saveHandler, Zend_Session::getSaveHandler());
    }
}
