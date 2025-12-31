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
require_once 'Zend/Controller/Request/Simple.php';

/**
 * Test class for Zend_Controller_Request_Simple.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Controller')]
#[PHPUnit\Framework\Attributes\Group('Zend_Controller_Request')]
class Zend_Controller_Request_SimpleTest extends PHPUnit\Framework\TestCase
{
    public function testSimpleRequestIsOfAbstractRequestType()
    {
        $request = new Zend_Controller_Request_Simple();
        $this->assertTrue($request instanceof Zend_Controller_Request_Abstract);
    }

    public function testSimpleReqestRetainsValuesPassedFromConstructor()
    {
        $request = new Zend_Controller_Request_Simple('test1', 'test2', 'test3', ['test4' => 'test5']);
        $this->assertEquals($request->getActionName(), 'test1');
        $this->assertEquals($request->getControllerName(), 'test2');
        $this->assertEquals($request->getModuleName(), 'test3');
        $this->assertEquals($request->getParam('test4'), 'test5');
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-3472')]
    public function testSettingParamToNullInSetparamsCorrectlyUnsetsValue()
    {
        $request = new Zend_Controller_Request_Simple();
        $request->setParam('key', 'value');
        $request->setParams([
            'key' => null,
        ]);
        $this->assertNull($request->getParam('key'));
    }
}
