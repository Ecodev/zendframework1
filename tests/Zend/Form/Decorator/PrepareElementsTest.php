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
require_once 'Zend/Form/SubForm.php';

/**
 * Test class for Zend_Form_Decorator_PrepareElements.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_PrepareElementsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_PrepareElementsTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->form = new Zend_Form();
        $this->form->setDecorators(['PrepareElements']);
        $this->decorator = $this->form->getDecorator('PrepareElements');
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

        return $view;
    }

    public function prepareForm()
    {
        $sub1 = new Zend_Form_SubForm();
        $sub1->addElement('text', 'foo')
            ->addElement('text', 'bar');

        $this->form->setElementsBelongTo('foo')
            ->addElement('text', 'foo')
            ->addElement('text', 'bar')
            ->addElement('text', 'baz')
            ->addElement('text', 'bat')
            ->addDisplayGroup(['baz', 'bat'], 'bazbat')
            ->addSubForm($sub1, 'sub')
            ->setView($this->getView());
    }

    public function testEachElementShouldHaveUpdatedBelongsToProperty()
    {
        $this->prepareForm();
        $this->form->render();
        $belongsTo = $this->form->getElementsBelongTo();
        foreach ($this->form->getElements() as $element) {
            $this->assertEquals($belongsTo, $element->getBelongsTo(), 'Tested element; wrong belongsTo');
        }
        foreach ($this->form->getSubForms() as $subForm) {
            $name = $subForm->getElementsBelongTo();
            foreach ($subForm->getElements() as $element) {
                $this->assertEquals($name, $element->getBelongsTo(), 'Tested sub element; wrong belongsTo; ' . $name . ': ' . $element->getName());
            }
        }
    }

    public function testEachElementShouldHaveUpdatedViewProperty()
    {
        $this->prepareForm();
        $this->form->render();
        $view = $this->form->getView();
        foreach ($this->form as $item) {
            $this->assertSame($view, $item->getView());
            if ($item instanceof Zend_Form) {
                foreach ($item->getElements() as $subItem) {
                    $this->assertSame($view, $subItem->getView(), var_export($subItem, 1));
                }
            }
        }
    }

    public function testEachElementShouldHaveUpdatedTranslatorProperty()
    {
        $this->prepareForm();
        $translator = new Zend_Translate('array', ['foo' => 'bar'], 'en');
        $this->form->setTranslator($translator);
        $this->form->render();
        $translator = $this->form->getTranslator();
        foreach ($this->form as $item) {
            $this->assertSame($translator, $item->getTranslator(), 'Translator not the same: ' . var_export($item->getTranslator(), 1));
            if ($item instanceof Zend_Form) {
                foreach ($item->getElements() as $subItem) {
                    $this->assertSame($translator, $subItem->getTranslator(), var_export($subItem, 1));
                }
            }
        }
    }
}
