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
require_once 'Zend/Layout.php';

/**
 * Test class for Zend_View_Helper_Json.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_JsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_JsonTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        Zend_View_Helper_JsonTest_Layout::resetMvcInstance();

        $this->response = new Zend_Controller_Response_Http();
        $this->response->headersSentThrowsException = false;

        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setResponse($this->response);

        $this->helper = new Zend_View_Helper_Json();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function verifyJsonHeader()
    {
        $value = null;
        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                if ($found) {
                    $this->fail('Content-Type header has been set twice.');

                    return null;
                }
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/json', $value);
    }

    public function testJsonHelperSetsResponseHeader()
    {
        $this->helper->json('foobar');
        $this->verifyJsonHeader();
    }

    /**
     * @group ZF-10675
     */
    public function testJsonHelperReplacesContentTypeReponseHeaderIfAlreadySet()
    {
        $this->response->setHeader('Content-Type', 'text/html');
        $this->helper->json('foobar');
        $this->verifyJsonHeader();
    }

    public function testJsonHelperReturnsJsonEncodedString()
    {
        $data = $this->helper->json(['foobar']);
        $this->assertTrue(is_string($data));
        $this->assertEquals(['foobar'], Zend_Json::decode($data));
    }

    public function testJsonHelperDisablesLayoutsByDefault()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $this->testJsonHelperReturnsJsonEncodedString();
        $this->assertFalse($layout->isEnabled());
    }

    public function testJsonHelperDoesNotDisableLayoutsWhenKeepLayoutFlagTrue()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $data = $this->helper->json(['foobar'], true);
        $this->assertTrue($layout->isEnabled());
    }

    /**
     * @group ZF-12397
     */
    public function testJsonHelperWithKeepLayoutAsArray()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $data = $this->helper->json(
            [
                'foobar',
            ],
            [
                'keepLayouts' => true,
                'encodeData' => false,
            ]
        );
        $this->assertTrue($layout->isEnabled());
        $this->assertSame(['foobar'], $data);
    }

    /**
     * @group ZF-10977
     */
    public function testJsonHelperWillAcceptPreencodedJson()
    {
        $data = $this->helper->json(Zend_Json::encode(['f']), false, false);
        $this->assertEquals('["f"]', $data);
    }

    /**
     * @group ZF-10977
     */
    public function testJsonHelperWillSendHeadersWhenProvidedWithPreencodedJson()
    {
        $data = $this->helper->json(Zend_Json::encode(['f']), false, false);
        $this->verifyJsonHeader();
    }
}

/**
 * Zend_Layout subclass to allow resetting MVC instance.
 */
#[AllowDynamicProperties]
class Zend_View_Helper_JsonTest_Layout extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}
