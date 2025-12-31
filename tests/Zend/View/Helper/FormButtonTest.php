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
require_once 'Zend/View/Helper/FormButton.php';

/**
 * Test class for Zend_View_Helper_FormButton.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
class Zend_View_Helper_FormButtonTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_View_Helper_FormButtonTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormButton();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testFormButtonRendersButtonXhtml()
    {
        $button = $this->helper->formButton('foo', 'bar');
        $this->assertMatchesRegularExpression('/<button[^>]*?value="bar"/', $button);
        $this->assertMatchesRegularExpression('/<button[^>]*?name="foo"/', $button);
        $this->assertMatchesRegularExpression('/<button[^>]*?id="foo"/', $button);
        $this->assertStringContainsString('</button>', $button);
    }

    public function testCanPassContentViaContentAttribKey()
    {
        $button = $this->helper->formButton('foo', 'bar', ['content' => 'Display this']);
        $this->assertStringContainsString('>Display this<', $button);
        $this->assertStringContainsString('<button', $button);
        $this->assertStringContainsString('</button>', $button);
    }

    public function testCanDisableContentEscaping()
    {
        $button = $this->helper->formButton('foo', 'bar', ['content' => '<b>Display this</b>', 'escape' => false]);
        $this->assertStringContainsString('><b>Display this</b><', $button);

        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'attribs' => ['content' => '<b>Display this</b>', 'escape' => false]]);
        $this->assertStringContainsString('><b>Display this</b><', $button);

        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'escape' => false, 'attribs' => ['content' => '<b>Display this</b>']]);
        $this->assertStringContainsString('><b>Display this</b><', $button);
        $this->assertStringContainsString('<button', $button);
        $this->assertStringContainsString('</button>', $button);
    }

    public function testValueUsedForContentWhenNoContentProvided()
    {
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar']);
        $this->assertMatchesRegularExpression('#<button[^>]*?value="bar"[^>]*>bar</button>#', $button);
    }

    public function testButtonTypeIsButtonByDefault()
    {
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar']);
        $this->assertStringContainsString('type="button"', $button);
    }

    public function testButtonTypeMayOnlyBeValidXhtmlButtonType()
    {
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'attribs' => ['type' => 'submit']]);
        $this->assertStringContainsString('type="submit"', $button);
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'attribs' => ['type' => 'reset']]);
        $this->assertStringContainsString('type="reset"', $button);
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'attribs' => ['type' => 'button']]);
        $this->assertStringContainsString('type="button"', $button);
        $button = $this->helper->formButton(['name' => 'foo', 'value' => 'bar', 'attribs' => ['type' => 'bogus']]);
        $this->assertStringContainsString('type="button"', $button);
    }
}
