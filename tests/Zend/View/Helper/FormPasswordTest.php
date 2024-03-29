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
require_once 'Zend/Registry.php';

/**
 * Zend_View_Helper_FormPasswordTest.
 *
 * Tests formPassword helper
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_FormPasswordTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_FormPasswordTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (Zend_Registry::isRegistered(\Zend_View_Helper_Doctype::class)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[\Zend_View_Helper_Doctype::class]);
        }
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormPassword();
        $this->helper->setView($this->view);
    }

    /**
     * @group ZF-1666
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formPassword([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertMatchesRegularExpression('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * @group ZF-1666
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formPassword([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertDoesNotMatchRegularExpression('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testShouldRenderAsHtmlByDefault()
    {
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertStringNotContainsString(' />', $test);
    }

    public function testShouldAllowRenderingAsXhtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertStringContainsString(' />', $test);
    }

    public function testShouldNotRenderValueByDefault()
    {
        $test = $this->helper->formPassword('foo', 'bar');
        $this->assertStringNotContainsString('bar', $test);
    }

    /**
     * @group ZF-2860
     */
    public function testShouldRenderValueWhenRenderPasswordFlagPresentAndTrue()
    {
        $test = $this->helper->formPassword('foo', 'bar', ['renderPassword' => true]);
        $this->assertStringContainsString('value="bar"', $test);
    }

    /**
     * @group ZF-2860
     */
    public function testRenderPasswordAttribShouldNeverBeRendered()
    {
        $test = $this->helper->formPassword('foo', 'bar', ['renderPassword' => true]);
        $this->assertStringNotContainsString('renderPassword', $test);
        $test = $this->helper->formPassword('foo', 'bar', ['renderPassword' => false]);
        $this->assertStringNotContainsString('renderPassword', $test);
    }
}
