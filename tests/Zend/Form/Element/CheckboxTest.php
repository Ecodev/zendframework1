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
require_once 'Zend/Form/Element/Checkbox.php';

/**
 * Test class for Zend_Form_Element_Checkbox.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_CheckboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_CheckboxTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_Checkbox('foo');
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
        require_once 'Zend/View.php';

        return new Zend_View();
    }

    public function testCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testCheckboxElementUsesCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formCheckbox', $helper);
    }

    public function testCheckedFlagIsFalseByDefault()
    {
        $this->assertFalse($this->element->checked);
    }

    public function testCheckedAttributeNotRenderedByDefault()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $html = $this->element->render($view);
        $this->assertStringNotContainsString('checked="checked"', $html);
    }

    public function testCheckedAttributeRenderedWhenCheckedFlagTrue()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $this->element->checked = true;
        $html = $this->element->render($view);
        $this->assertStringContainsString('checked="checked"', $html);
    }

    public function testCheckedValueDefaultsToOne()
    {
        $this->assertEquals(1, $this->element->getCheckedValue());
    }

    public function testUncheckedValueDefaultsToZero()
    {
        $this->assertEquals(0, $this->element->getUncheckedValue());
    }

    public function testCanSetCheckedValue()
    {
        $this->testCheckedValueDefaultsToOne();
        $this->element->setCheckedValue('foo');
        $this->assertEquals('foo', $this->element->getCheckedValue());
    }

    public function testCanSetUncheckedValue()
    {
        $this->testUncheckedValueDefaultsToZero();
        $this->element->setUncheckedValue('foo');
        $this->assertEquals('foo', $this->element->getUncheckedValue());
    }

    public function testValueInitiallyUncheckedValue()
    {
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToCheckedValueSetsWithEquivalentValue()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setValue($this->element->getCheckedValue());
        $this->assertEquals($this->element->getCheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToAnythingOtherThanCheckedValueSetsAsUncheckedValue()
    {
        $this->testSettingValueToCheckedValueSetsWithEquivalentValue();
        $this->element->setValue('bogus');
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingCheckedFlagToTrueSetsValueToCheckedValue()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setChecked(true);
        $this->assertEquals($this->element->getCheckedValue(), $this->element->getValue());
    }

    public function testSettingCheckedFlagToFalseSetsValueToUncheckedValue()
    {
        $this->testSettingCheckedFlagToTrueSetsValueToCheckedValue();
        $this->element->setChecked(false);
        $this->assertEquals($this->element->getUncheckedValue(), $this->element->getValue());
    }

    public function testSettingValueToCheckedValueMarksElementAsChecked()
    {
        $this->testValueInitiallyUncheckedValue();
        $this->element->setValue($this->element->getCheckedValue());
        $this->assertTrue($this->element->checked);
    }

    public function testSettingValueToUncheckedValueMarksElementAsNotChecked()
    {
        $this->testSettingValueToCheckedValueMarksElementAsChecked();
        $this->element->setValue($this->element->getUncheckedValue());
        $this->assertFalse($this->element->checked);
    }

    public function testSetOptionsSetsInitialValueAccordingToCheckedAndUncheckedValues()
    {
        $options = [
            'checkedValue' => 'foo',
            'uncheckedValue' => 'bar',
        ];

        $element = new Zend_Form_Element_Checkbox('test', $options);
        $this->assertEquals($options['uncheckedValue'], $element->getValue());
    }

    public function testSetOptionsSetsInitialValueAccordingToSubmittedValues()
    {
        $options = [
            'test1' => [
                'value' => 'foo',
                'checkedValue' => 'foo',
                'uncheckedValue' => 'bar',
            ],
            'test2' => [
                'value' => 'bar',
                'checkedValue' => 'foo',
                'uncheckedValue' => 'bar',
            ],
        ];

        foreach ($options as $current) {
            $element = new Zend_Form_Element_Checkbox('test', $current);
            $this->assertEquals($current['value'], $element->getValue());
            $this->assertEquals($current['checkedValue'], $element->getCheckedValue());
            $this->assertEquals($current['uncheckedValue'], $element->getUncheckedValue());
        }
    }

    public function testCheckedValueAlwaysRenderedAsCheckboxValue()
    {
        $this->element->setValue($this->element->getUncheckedValue());
        $html = $this->element->render($this->getView());
        if (!preg_match_all('/(<input[^>]+>)/', $html, $matches)) {
            $this->fail('Unexpected generated HTML: ' . $html);
        }
        $this->assertEquals(2, is_countable($matches[1]) ? count($matches[1]) : 0);
        foreach ($matches[1] as $element) {
            if (strstr($element, 'hidden')) {
                $this->assertStringContainsString($this->element->getUncheckedValue(), $element);
            } else {
                $this->assertStringContainsString($this->element->getCheckedValue(), $element);
            }
        }
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
