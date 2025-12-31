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
require_once 'Zend/Form/Element/Hash.php';

/**
 * Test class for Zend_Form_Element_Hash.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Form')]
class Zend_Form_Element_HashTest extends PHPUnit\Framework\TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        if (isset($this->hash)) {
            unset($this->hash);
        }

        $session = new Zend_Form_Element_HashTest_SessionContainer();
        $session->hash = null;

        $this->element = new Zend_Form_Element_Hash('foo', [
            'session' => $session,
        ]);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testHashElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testHashElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testSaltPopulatedByDefault()
    {
        $salt = $this->element->getSalt();
        $this->assertFalse(empty($salt));
    }

    public function testCanSetSalt()
    {
        $salt = $this->element->getSalt();
        $this->element->setSalt('foobar');
        $this->assertNotEquals($salt, $this->element->getSalt());
        $this->assertEquals('foobar', $this->element->getSalt());
    }

    public function testTimeoutPopulatedByDefault()
    {
        $ttl = $this->element->getTimeout();
        $this->assertFalse(empty($ttl));
        $this->assertTrue(is_int($ttl));
    }

    public function testCanSetTimeout()
    {
        $ttl = $this->element->getTimeout();
        $this->element->setTimeout(3600);
        $this->assertNotEquals($ttl, $this->element->getTimeout());
        $this->assertEquals(3600, $this->element->getTimeout());
    }

    public function testGetHashReturnsHashValue()
    {
        $hash = $this->element->getHash();
        $this->assertFalse(empty($hash));
        $this->assertTrue(is_string($hash));
        $this->hash = $hash;
    }

    public function testGetHashSetsElementValueToHash()
    {
        $this->testGetHashReturnsHashValue();
        $this->assertEquals($this->hash, $this->element->getValue());
    }

    public function testHashIsMd5()
    {
        $this->testGetHashReturnsHashValue();
        $this->assertEquals(32, strlen($this->hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $this->hash);
    }

    public function testLabelIsNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testSessionNameContainsSaltAndName()
    {
        $sessionName = $this->element->getSessionName();
        $this->assertStringContainsString($this->element->getSalt(), $sessionName);
        $this->assertStringContainsString($this->element->getName(), $sessionName);
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');

        return $view;
    }

    public function testValidatorTokenReceivesSessionHashWhenPresent()
    {
        $this->_checkZf2794();

        $session = $this->element->getSession();
        $session->hash = $this->element->getHash();
        $element = new Zend_Form_Element_Hash('foo', ['session' => $session]);
        $validator = $element->getValidator('Identical');
        $this->assertEquals($session->hash, $validator->getToken());
    }

    public function testRenderInitializesSessionHashToken()
    {
        $session = $this->element->getSession();
        $this->assertNull($session->hash);
        $html = $this->element->render($this->getView());

        $this->assertEquals($this->element->getHash(), $session->hash);
        $this->assertEquals(1, $session->setExpirationHops);
        $this->assertEquals($this->element->getTimeout(), $session->setExpirationSeconds);
    }

    public function testHashTokenIsRendered()
    {
        $html = $this->element->render($this->getView());
        $this->assertStringContainsString($this->element->getHash(), $html);
    }

    public function testHiddenInputRenderedByDefault()
    {
        $html = $this->element->render($this->getView());
        $this->assertMatchesRegularExpression('/<input[^>]*?type="hidden"/', $html, $html);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-7404')]
    public function testShouldRenderHashTokenIfRenderedThroughMagicCall()
    {
        $this->element->setView($this->getView());
        $html = $this->element->renderViewHelper();
        $this->assertStringContainsString($this->element->getHash(), $html, 'Html is: ' . $html);
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete.
     *
     * @see   http://framework.zend.com/issues/browse/ZF-2794
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.1.4', '=')) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }
}

#[AllowDynamicProperties]
class Zend_Form_Element_HashTest_SessionContainer
{
    protected static $_hash;

    public function __get($name)
    {
        if ('hash' == $name) {
            return self::$_hash;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if ('hash' == $name) {
            self::$_hash = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (('hash' == $name) && (null !== self::$_hash)) {
            return true;
        }

        return false;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'setExpirationHops':
            case 'setExpirationSeconds':
                $this->$method = array_shift($args);

                break;
            default:
        }
    }
}
