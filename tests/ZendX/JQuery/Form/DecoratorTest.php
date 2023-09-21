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
require_once 'ZendX/JQuery/View/Helper/JQuery.php';

require_once 'ZendX/JQuery/Form.php';
require_once 'ZendX/JQuery/Form/Element/Spinner.php';
require_once 'ZendX/JQuery/Form/Decorator/UiWidgetElement.php';
require_once 'ZendX/JQuery/Form/Decorator/TabContainer.php';
require_once 'ZendX/JQuery/Form/Decorator/TabPane.php';

class ZendX_JQuery_Form_DecoratorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        Zend_Registry::_unsetInstance();
    }

    /**
     * Returns the contens of the exepcted $file.
     *
     * @param string $file
     *
     * @return string
     */
    protected function _getExpected($file)
    {
        return file_get_contents(__DIR__ . '/_files/expected/' . $file);
    }

    public function testUiWidgetElementDecoratorRender()
    {
        $ac = new ZendX_JQuery_Form_Element_Spinner('ac1');
        // Remove all non jQUery related decorators
        $ac->removeDecorator('Errors');
        $ac->removeDecorator('HtmlTag');
        $ac->removeDecorator('Label');

        try {
            $ac->render();
            $this->fail();
        } catch (Zend_Form_Decorator_Exception $e) {
        } catch (Zend_Exception $e) {
            $this->fail();
        }

        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        $ac->setView($view);
        $output = $ac->render();

        $this->assertStringContainsString('ac1', $output);
    }

    public function testUiWidgetElementJQueryParams()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('ac1');
        $uiWidget = $spinner->getDecorator('UiWidgetElement');

        $uiWidget->setJQueryParam('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $uiWidget->getJQueryParams());

        $uiWidget->setJQueryParams(['bar' => 'baz']);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $uiWidget->getJQueryParams());

        $this->assertEquals('bar', $uiWidget->getJQueryParam('foo'));
        $this->assertEquals('baz', $uiWidget->getJQueryParam('bar'));
        $this->assertNull($uiWidget->getJQueryParam('unknownParam'));
    }

    public function testUiWidgetElementRendersElementJQueryParams()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        $spinner = new ZendX_JQuery_Form_Element_Spinner('ac1');
        $spinner->setJQueryParam('min', 100);
        $spinner->setView($view);
        $output = $spinner->render();
        $this->assertEquals(['$("#ac1").spinner({"min":100});'], $view->jQuery()->getOnLoadActions());
    }

    public function testUiWidgetContainerGetHelper()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $this->assertEquals('tabContainer', $container->getHelper());
    }

    public function testUiWidgetContainerGetAttribs()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $ac = new ZendX_JQuery_Form_Element_Spinner('ac1');
        $container->setElement($ac);

        $this->assertEquals(['options' => []], $container->getAttribs());
    }

    public function testUiWidgetContainerGetJQueryParams()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $ac = new ZendX_JQuery_Form_Element_Spinner('spinner');
        $ac->setJQueryParams(['foo' => 'bar', 'baz' => 'baz']);
        $container->setElement($ac);

        $this->assertEquals(['foo' => 'bar', 'baz' => 'baz'], $container->getJQueryParams());
    }

    public function testUiWidgetPaneRenderingThrowsExceptionWithoutContainerIdOption()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');
        $spinner->setView(new Zend_View());
        $spinner->setJQueryParam('title', 'Title');

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        try {
            $pane->render('');
            $this->fail();
        } catch (Zend_Form_Decorator_Exception $e) {
        }
        self::assertTrue(true);
    }

    public function testUiWidgetPaneRenderingThrowsExceptionWithoutTitleOption()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');
        $spinner->setView(new Zend_View());
        $spinner->setJQueryParam('containerId', 'xyzId');

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        try {
            $pane->render('');
            $this->fail();
        } catch (Zend_Form_Decorator_Exception $e) {
            self::assertTrue(true);
        }
    }

    public function testUiWidgetPaneRenderingNoPaneWhenElementHasNoView()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinner1');

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        $this->assertEquals('justthis', $pane->render('justthis'));
    }

    public function testUiWidgetContainerRender()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create new jQuery Form
        $form = new ZendX_JQuery_Form();
        $form->setView($view);
        $form->setAction('formdemo.php');
        $form->setAttrib('id', 'mainForm');

        // Use a TabContainer for your form:
        $form->setDecorators([
            'FormElements',
            ['TabContainer', [
                'id' => 'tabContainer',
                'style' => 'width: 600px;',
                'jQueryParams' => [
                    'tabPosition' => 'top',
                ],
            ]],
            'Form',
        ]);

        $subForm1 = new ZendX_JQuery_Form('subform1');
        $subForm1->setView($view);

        // Add Element Spinner
        $elem = new ZendX_JQuery_Form_Element_Spinner('spinner1', ['label' => 'Spinner:', 'attribs' => ['class' => 'flora']]);
        $elem->setJQueryParams(['min' => 0, 'max' => 1000, 'start' => 100]);

        $subForm1->addElement($elem);

        $subForm1->setDecorators([
            'FormElements',
            ['HtmlTag', ['tag' => 'dl']],
            ['TabPane', ['jQueryParams' => ['containerId' => 'mainForm', 'title' => 'Slider']]],
        ]);

        $form->addSubForm($subForm1, 'form1');

        $output = $form->render($view);
        $this->assertStringContainsString('id="tabContainer"', $output);
        $this->assertStringContainsString('href="#tabContainer-frag-1"', $output);
        $this->assertStringContainsString('id="tabContainer-frag-1"', $output);
    }

    /**
     * @group ZF-12175
     */
    public function testUiWidgetContainerRenderWithContent()
    {
        // Setup view
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create jQuery Form
        $form = new ZendX_JQuery_Form(
            [
                'method' => Zend_Form::METHOD_GET,
                'attribs' => [
                    'id' => 'mainForm',
                ],
                'decorators' => [
                    'FormElements',
                    [
                        'HtmlTag',
                        [
                            'tag' => 'dl',
                        ],
                    ],
                    [
                        'TabContainer',
                        [
                            'id' => 'tabContainer',
                            'placement' => 'prepend',
                            'separator' => '',
                        ],
                    ],
                    'Form',
                ],
            ]
        );

        // Add sub form
        $subForm = new ZendX_JQuery_Form(
            [
                'decorators' => [
                    'FormElements',
                    [
                        'HtmlTag',
                        [
                            'tag' => 'dl',
                        ],
                    ],
                    [
                        'TabPane',
                        [
                            'jQueryParams' => [
                                'containerId' => 'mainForm',
                                'title' => 'Slider',
                            ],
                        ],
                    ],
                ],
            ]
        );
        $form->addSubForm($subForm, 'subform');

        // Add spinner element to subform
        $subForm->addElement(
            'spinner',
            'spinner',
            [
                'label' => 'Spinner:',
                'attribs' => [
                    'class' => 'flora',
                ],
                'jQueryParams' => [
                    'min' => 0,
                    'max' => 1000,
                    'start' => 100,
                ],
            ]
        );

        // Add submit button to main form
        $form->addElement(
            'submit',
            'submit',
            [
                'label' => 'Send',
            ]
        );

        $this->assertSame(
            $this->_getExpected('uiwidgetcontainer/with_content.html'),
            $form->render($view)
        );
    }

    /**
     * @group ZF-8055
     */
    public function testUiWidgetDialogContainerRenderBug()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create new jQuery Form
        $form = new ZendX_JQuery_Form();
        $form->setView($view);
        $form->setAction('formdemo.php');
        $form->setAttrib('id', 'mainForm');

        // Use a TabContainer for your form:
        $form->setDecorators([
            'FormElements',
            'Form',
            ['DialogContainer', [
                'id' => 'tabContainer',
                'style' => 'width: 600px;',
                'jQueryParams' => [
                    'tabPosition' => 'top',
                ],
            ]],
        ]);

        $subForm1 = new ZendX_JQuery_Form('subform1');
        $subForm1->setView($view);

        // Add Element Spinner
        $elem = new ZendX_JQuery_Form_Element_Spinner('spinner1', ['label' => 'Spinner:', 'attribs' => ['class' => 'flora']]);
        $elem->setJQueryParams(['min' => 0, 'max' => 1000, 'start' => 100]);

        $subForm1->addElement($elem);

        $subForm1->setDecorators([
            'FormElements',
            ['HtmlTag', ['tag' => 'dl']],
        ]);

        $form->addSubForm($subForm1, 'form1');

        $output = $form->render($view);

        $this->assertStringContainsString('<div id="tabContainer" style="width: 600px;"><form', $output);
    }

    public function testRenderWidgetElementShouldEnableJQueryHelper()
    {
        $view = new Zend_View();

        $widget = new ZendX_JQuery_Form_Element_Spinner('spinner1', ['label' => 'Spinner']);
        $widget->setView($view);

        $view->jQuery()->disable();
        $view->jQuery()->uiDisable();

        $widget->render();

        $this->assertTrue($view->jQuery()->isEnabled());
        $this->assertTrue($view->jQuery()->uiIsEnabled());
    }

    public function testSettingWidgetPlacement()
    {
        $view = new Zend_View();
        $widget = new ZendX_JQuery_Form_Element_Spinner('spinner1');
        $widget->setView($view);
        $widget->getDecorator('UiWidgetElement')->setOption('separator', '[SEP]');

        $widget->getDecorator('UiWidgetElement')->setOption('placement', 'APPEND');
        $html = $widget->render();
        $this->assertStringContainsString('[SEP]<input type="text" name="spinner1" id="spinner1" value="">', $html);

        $widget->getDecorator('UiWidgetElement')->setOption('placement', 'PREPEND');
        $html = $widget->render();
        $this->assertStringContainsString('<input type="text" name="spinner1" id="spinner1" value="">[SEP]', $html);
    }
}
