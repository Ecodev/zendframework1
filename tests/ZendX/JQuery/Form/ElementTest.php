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
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version     $Id: AllTests.php 11232 2008-09-05 08:16:33Z beberlei $
 */
require_once __DIR__ . '/../../../TestHelper.php';

require_once 'ZendX/JQuery.php';
require_once 'ZendX/JQuery/Form.php';
require_once 'ZendX/JQuery/View/Helper/JQuery.php';

require_once 'ZendX/JQuery/Form/Element/Spinner.php';
require_once 'ZendX/JQuery/Form/Element/Slider.php';
require_once 'ZendX/JQuery/Form/Element/DatePicker.php';
require_once 'ZendX/JQuery/Form/Element/AutoComplete.php';

require_once 'ZendX/JQuery/Form/Decorator/UiWidgetElement.php';

class ZendX_JQuery_Form_ElementTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        Zend_Registry::_unsetInstance();
    }

    public function testElementSetGetJQueryParam()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');
        $spinner->setJQueryParam('foo', 'baz');
        $this->assertEquals('baz', $spinner->getJQueryParam('foo'));

        $spinner->setJQueryParam('foo', 'bar');
        $spinner->setJQueryParam('bar', []);
        $this->assertEquals('bar', $spinner->getJQueryParam('foo'));
        $this->assertEquals([], $spinner->getJQueryParam('bar'));
    }

    public function testElementSetGetMassJQueryParams()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');

        $spinner->setJQueryParams(['foo' => 'baz', 'bar' => 'baz']);
        $this->assertEquals(['foo' => 'baz', 'bar' => 'baz'], $spinner->getJQueryParams());

        $spinner->setJQueryParams(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $spinner->getJQueryParams());
    }

    public function testElementsHaveUiWidgetDecorator()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');
        $this->assertTrue($spinner->getDecorator('UiWidgetElement') !== false);

        $slider = new ZendX_JQuery_Form_Element_Slider('sliderElem');
        $this->assertTrue($slider->getDecorator('UiWidgetElement') !== false);

        $dp = new ZendX_JQuery_Form_Element_DatePicker('dpElem');
        $this->assertTrue($dp->getDecorator('UiWidgetElement') !== false);

        $ac = new ZendX_JQuery_Form_Element_AutoComplete('acElem');
        $this->assertTrue($ac->getDecorator('UiWidgetElement') !== false);
    }

    public function testElementsEnableJQueryViewPath()
    {
        $view = new Zend_View();
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');

        $this->assertFalse(false !== $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'));
        $spinner->setView($view);
        $this->assertTrue(false !== $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'));
    }

    /**
     * @group ZF-4694
     */
    public function testJQueryElementWithOnlyViewHelperIsNotAllowedToDieZf4694()
    {
        $view = new Zend_View();

        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');
        $spinner->setDecorators(['ViewHelper']);
        $spinner->setView($view);

        try {
            $spinner->render();
            $this->fail();
        } catch (ZendX_JQuery_Form_Exception $e) {
            // success here
        } catch (Exception $e) {
            $this->fail();
        }
        self::assertTrue(true);
    }

    /**
     * @group ZF-5125
     */
    public function testJQueryElementHasToImplementMarkerInterface()
    {
        $view = new Zend_View();

        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');
        $spinner->setDecorators(['ViewHelper']);
        $spinner->setView($view);

        try {
            $spinner->render();
            $this->fail();
        } catch (ZendX_JQuery_Form_Exception $e) {
            // success here
        }

        $spinner->setDecorators(['UiWidgetElement']);

        try {
            $spinner->render();
            // success here
        } catch (ZendX_JQuery_Form_Exception $e) {
            $this->fail();
        }
        self::assertTrue(true);
    }

    /**
     * @group ZF-4859
     */
    public function testAutocompleteDoesNotDoubleArrayEncodeDataJsonField()
    {
        $view = new Zend_View();
        $form = new ZendX_JQuery_Form();

        $dataSource = [0 => 'John Doe'];

        $lastname = new ZendX_JQuery_Form_Element_AutoComplete('Lastname', ['label' => 'Lastname']);
        $form->addElement($lastname);
        $form->Lastname->setJQueryParam('source', $dataSource);

        Zend_Json::$useBuiltinEncoderDecoder = true;
        $output = $form->render($view);

        $this->assertEquals(
            ['$("#Lastname").autocomplete({"source":["John Doe"]});'],
            $view->jQuery()->getOnLoadActions()
        );

        Zend_Json::$useBuiltinEncoderDecoder = false;
        $output = $form->render($view);
        $this->assertEquals(
            ['$("#Lastname").autocomplete({"source":["John Doe"]});'],
            $view->jQuery()->getOnLoadActions()
        );
    }

    /**
     * @group ZF-5043
     */
    public function testFormWithoutIdButSubformsProducesArrayNotationWhichWontWork()
    {
        $view = new Zend_View();
        $form = new ZendX_JQuery_Form();

        $datePicker = new ZendX_JQuery_Form_Element_DatePicker('dp1');

        $subform = new Zend_Form_SubForm();
        $subform->addElement($datePicker);

        $form->addSubForm($subform, 'sf1');
        $form->setIsArray(true);

        $form = $form->render($view);
        $jquery = $view->jQuery()->__toString();
        $this->assertStringContainsString('sf1[dp1]', $form);
        $this->assertStringNotContainsString('$("#sf1[dp1]")', $jquery);
    }

    /**
     * @group ZF-6979
     */
    public function testDatePickerWithDescriptionDecorator()
    {
        $view = new Zend_View();

        $datePicker = new ZendX_JQuery_Form_Element_DatePicker('dp1');
        $datePicker->addDecorator(new Zend_Form_Decorator_Description());
        $datePicker->setDescription('foo');

        $html = $datePicker->render($view);

        $this->assertStringContainsString('<p class="description">foo</p>', $html);
    }

    public function testGetDefaultDecorators()
    {
        $widget = new ZendX_JQuery_Form_Element_DatePicker('dp1');
        $decorators = $widget->getDecorators();
        $this->assertEquals(5, is_countable($decorators) ? count($decorators) : 0);

        $this->assertTrue(
            $decorators['ZendX_JQuery_Form_Decorator_UiWidgetElement'] instanceof ZendX_JQuery_Form_Decorator_UiWidgetElement
        );
        $this->assertTrue(
            $decorators[\Zend_Form_Decorator_Errors::class] instanceof Zend_Form_Decorator_Errors
        );
        $this->assertTrue(
            $decorators[\Zend_Form_Decorator_Description::class] instanceof Zend_Form_Decorator_Description
        );
        $this->assertTrue(
            $decorators[\Zend_Form_Decorator_HtmlTag::class] instanceof Zend_Form_Decorator_HtmlTag
        );
        $this->assertTrue(
            $decorators[\Zend_Form_Decorator_Label::class] instanceof Zend_Form_Decorator_Label
        );
    }
}
