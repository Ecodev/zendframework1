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
require_once 'Zend/Form/Element/Radio.php';

/**
 * Test class for Zend_Form_Element_Radio.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_RadioTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_RadioTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_Radio('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');

        return $view;
    }

    public function testRadioElementSubclassesMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testRadioElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testRadioElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testRadioElementIsNotAnArrayByDefault()
    {
        $this->assertFalse($this->element->isArray());
    }

    public function testHelperAttributeSetToFormRadioByDefault()
    {
        $this->assertEquals('formRadio', $this->element->getAttrib('helper'));
    }

    public function testRadioElementUsesRadioHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formRadio', $helper);
    }

    public function testCanDisableIndividualRadioOptions()
    {
        $this->element->setMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
            'bat' => 'Bat',
            'test' => 'Test',
        ])
            ->setAttrib('disable', ['baz', 'test']);
        $html = $this->element->render($this->getView());
        foreach (['baz', 'test'] as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertMatchesRegularExpression('/<input[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (['foo', 'bar', 'bat'] as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertDoesNotMatchRegularExpression('/<input[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    public function testSpecifiedSeparatorIsUsedWhenRendering()
    {
        $this->element->setMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
            'bat' => 'Bat',
            'test' => 'Test',
        ])
            ->setSeparator('--FooBarFunSep--');
        $html = $this->element->render($this->getView());
        $this->assertStringContainsString($this->element->getSeparator(), $html);
        $count = substr_count($html, $this->element->getSeparator());
        $this->assertEquals(4, $count);
    }

    public function testRadioElementRendersDtDdWrapper()
    {
        $this->element->setMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
            'bat' => 'Bat',
            'test' => 'Test',
        ]);
        $html = $this->element->render($this->getView());
        $this->assertMatchesRegularExpression('#<dt[^>]*>&\#160;</dt>.*?<dd#s', $html, $html);
    }

    /**
     * @group ZF-9682
     */
    public function testCustomLabelDecorator()
    {
        $form = new Zend_Form();
        $form->addElementPrefixPath('My_Decorator', __DIR__ . '/../_files/decorators/', 'decorator');

        $form->addElement($this->element);

        $element = $form->getElement('foo');

        $this->assertTrue(
            $element->getDecorator('Label') instanceof My_Decorator_Label
        );
    }

    /**
     * @group ZF-6426
     */
    public function testRenderingShouldCreateLabelWithoutForAttribute()
    {
        $this->element->setMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
        ])
            ->setLabel('Foo');
        $html = $this->element->render($this->getView());
        $this->assertStringNotContainsString('for="foo"', $html);
    }

    /**
     * @group ZF-11517
     */
    public function testCreationWithIndividualDecoratorsAsConstructorOptionsWithoutLabel()
    {
        $element = new Zend_Form_Element_Radio([
            'name' => 'foo',
            'multiOptions' => [
                'bar' => 'Bar',
                'baz' => 'Baz',
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->assertFalse($element->getDecorator('label'));
    }

    /**
     * @group ZF-11517
     */
    public function testRenderingWithIndividualDecoratorsAsConstructorOptionsWithoutLabel()
    {
        $element = new Zend_Form_Element_Radio([
            'name' => 'foo',
            'multiOptions' => [
                'bar' => 'Bar',
                'baz' => 'Baz',
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $html = $element->render($this->getView());
        $this->assertStringNotContainsString('<dt id="foo-label">&#160;</dt>', $html);
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

    /**
     * Prove the fluent interface on Zend_Form_Element_Radio::loadDefaultDecorators.
     *
     * @see http://framework.zend.com/issues/browse/ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }
}
