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
 * @version $Id$
 */
require_once 'Zend/Registry.php';

/**
 * Zend_View_Helper_FormTextTest.
 *
 * Tests formText helper, including some common functionality of all form helpers
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_FormTextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_FormTextTest');
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
        $this->helper = new Zend_View_Helper_FormText();
        $this->helper->setView($this->view);
    }

    public function testIdSetFromName()
    {
        $element = $this->helper->formText('foo');
        $this->assertStringContainsString('name="foo"', $element);
        $this->assertStringContainsString('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $element = $this->helper->formText('foo', null, ['id' => 'bar']);
        $this->assertStringContainsString('name="foo"', $element);
        $this->assertStringContainsString('id="bar"', $element);
    }

    public function testSetValue()
    {
        $element = $this->helper->formText('foo', 'bar');
        $this->assertStringContainsString('name="foo"', $element);
        $this->assertStringContainsString('value="bar"', $element);
    }

    public function testReadOnlyAttribute()
    {
        $element = $this->helper->formText('foo', null, ['readonly' => 'readonly']);
        $this->assertStringContainsString('readonly="readonly"', $element);
    }

    /**
     * ZF-1666.
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formText([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertMatchesRegularExpression('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-1666.
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formText([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertDoesNotMatchRegularExpression('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->formText('foo', 'bar');
        $this->assertStringNotContainsString(' />', $test);
    }

    public function testCanRendersAsXHtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formText('foo', 'bar');
        $this->assertStringContainsString(' />', $test);
    }
}
