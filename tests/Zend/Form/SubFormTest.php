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

// error_reporting(E_ALL);

require_once 'Zend/View.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_SubFormTest extends \PHPUnit\Framework\TestCase
{
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_SubFormTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        Zend_Form::setDefaultTranslator(null);

        $this->form = new Zend_Form_SubForm();
    }

    public function tearDown(): void
    {
    }

    // General
    public function testSubFormUtilizesDefaultDecorators()
    {
        $decorators = $this->form->getDecorators();
        $this->assertTrue(array_key_exists(\Zend_Form_Decorator_FormElements::class, $decorators));
        $this->assertTrue(array_key_exists(\Zend_Form_Decorator_HtmlTag::class, $decorators));
        $this->assertTrue(array_key_exists(\Zend_Form_Decorator_Fieldset::class, $decorators));
        $this->assertTrue(array_key_exists(\Zend_Form_Decorator_DtDdWrapper::class, $decorators));

        $htmlTag = $decorators[\Zend_Form_Decorator_HtmlTag::class];
        $tag = $htmlTag->getOption('tag');
        $this->assertEquals('dl', $tag);
    }

    public function testSubFormIsArrayByDefault()
    {
        $this->assertTrue($this->form->isArray());
    }

    public function testElementsBelongToSubFormNameByDefault()
    {
        $this->testSubFormIsArrayByDefault();
        $this->form->setName('foo');
        $this->assertEquals($this->form->getName(), $this->form->getElementsBelongTo());
    }

    // Extensions

    public function testInitCalledBeforeLoadDecorators()
    {
        $form = new Zend_Form_SubFormTest_SubForm();
        $decorators = $form->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    // Bugfixes

    /**
     * @group ZF-2883
     */
    public function testDisplayGroupsShouldInheritSubFormNamespace()
    {
        $this->form->addElement('text', 'foo')
            ->addElement('text', 'bar')
            ->addDisplayGroup(['foo', 'bar'], 'foobar');

        $form = new Zend_Form();
        $form->addSubForm($this->form, 'attributes');
        $html = $form->render(new Zend_View());

        $this->assertStringContainsString('name="attributes[foo]"', $html);
        $this->assertStringContainsString('name="attributes[bar]"', $html);
    }

    /**
     * @group ZF-3272
     */
    public function testRenderedSubFormDtShouldContainNoBreakSpace()
    {
        $subForm = new Zend_Form_SubForm([
            'elements' => [
                'foo' => 'text',
                'bar' => 'text',
            ],
        ]);
        $form = new Zend_Form();
        $form->addSubForm($subForm, 'foobar')
            ->setView(new Zend_View());
        $html = $form->render();
        $this->assertStringContainsString('>&#160;</dt>', $html);
    }

    /**
     * Prove the fluent interface on Zend_Form_Subform::loadDefaultDecorators.
     *
     * @see http://framework.zend.com/issues/browse/ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->form, $this->form->loadDefaultDecorators());
    }

    /**
     * @see ZF-11504
     */
    public function testSubFormWithNumericName()
    {
        $subForm = new Zend_Form_SubForm([
            'elements' => [
                'foo' => 'text',
                'bar' => 'text',
            ],
        ]);
        $form = new Zend_Form();
        $form->addSubForm($subForm, 0);
        $form->addSubForm($subForm, 234);
        $form2 = clone $form;
        $this->assertEquals($form2->getSubForm(234)->getName(),234);
        $this->assertEquals($form2->getSubForm(0)->getName(),0);
    }
}

#[AllowDynamicProperties]
class Zend_Form_SubFormTest_SubForm extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}
