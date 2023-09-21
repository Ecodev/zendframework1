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
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_FormErrors.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_FormErrorsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_FormErrorsTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_FormErrors();
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

    public function setupForm()
    {
        $form1 = new Zend_Form_SubForm();
        $form1->addElement('text', 'foo', [
            'label' => 'Sub Foo: ',
            'required' => true,
            'validators' => [
                'NotEmpty',
                'Alpha',
            ],
        ])
            ->addElement('text', 'bar', [
                'label' => 'Sub Bar: ',
                'required' => true,
                'validators' => [
                    'Alpha',
                    'Alnum',
                ],
            ]);
        $form2 = new Zend_Form();
        $form2->addElement('text', 'foo', [
            'label' => 'Master Foo: ',
            'required' => true,
            'validators' => [
                'NotEmpty',
                'Alpha',
            ],
        ])
            ->addElement('text', 'bar', [
                'required' => true,
                'validators' => [
                    'Alpha',
                    'Alnum',
                ],
            ])
            ->addSubForm($form1, 'sub');
        $form2->isValid([
            'foo' => '',
            'bar' => 'foo 2 u 2',
            'sub' => [
                'foo' => '',
                'bar' => 'foo 2 u 2',
            ],
        ]);
        $form2->setView($this->getView());
        $this->decorator->setElement($form2);
        $this->form = $form2;

        return $form2;
    }

    public function testRenderReturnsInitialContentIfNoViewPresentInForm()
    {
        $form = new Zend_Form();
        $this->decorator->setElement($form);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testNotGeneratingSubformErrorMarkupWrappingWhenNoErrors()
    {
        $form1 = new Zend_Form_SubForm();
        $form2 = new Zend_Form();
        $form2->addSubForm($form1, 'sub');
        $form2->setView($this->getView());
        $this->decorator->setElement($form2);

        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testRenderRendersAllErrorMessages()
    {
        $this->setupForm();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content, $test);
        foreach ($this->form->getMessages() as $name => $messages) {
            foreach ($messages as $key => $message) {
                if (is_string($message)) {
                    $this->assertStringContainsString($message, $test, var_export($messages, 1));
                } else {
                    foreach ($message as $m) {
                        $this->assertStringContainsString($m, $test, var_export($messages, 1));
                    }
                }
            }
        }
    }

    public function testRenderAppendsMessagesToContentByDefault()
    {
        $this->setupForm();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertMatchesRegularExpression('#' . $content . '.*?<ul#s', $test, $test);
    }

    public function testRenderPrependsMessagesToContentWhenRequested()
    {
        $this->decorator->setOptions(['placement' => 'PREPEND']);
        $this->setupForm();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertMatchesRegularExpression('#</ul>.*?' . $content . '#s', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithPhpEolByDefault()
    {
        $this->setupForm();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content . PHP_EOL . '<ul', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithCustomSeparatorWhenRequested()
    {
        $this->decorator->setOptions(['separator' => '<br />']);
        $this->setupForm();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content . $this->decorator->getSeparator() . '<ul', $test, $test);
    }

    public function testIgnoreSubFormsFlagShouldBeFalseByDefault()
    {
        $this->assertFalse($this->decorator->ignoreSubForms());
    }

    public function testLabelsShouldBeUsed()
    {
        $this->setupForm();
        $markup = $this->decorator->render('');
        $this->assertStringContainsString('>Sub Foo: </b>', $markup, $markup);
        $this->assertStringContainsString('>Sub Bar: </b>', $markup, $markup);
        $this->assertStringContainsString('>Master Foo: </b>', $markup);
        $this->assertStringNotContainsString('>Master Bar: </b>', $markup);
        $this->assertStringContainsString('>bar</b>', $markup);
    }

    public function testMarkupOptionsMayBePassedViaSetOptions()
    {
        $options = [
            'ignoreSubForms' => true,
            'markupElementLabelEnd' => '</i>',
            'markupElementLabelStart' => '<i>',
            'markupListEnd' => '</dl>',
            'markupListItemEnd' => '</dd>',
            'markupListItemStart' => '<dd>',
            'markupListStart' => '<dl class="form-errors">',
        ];
        $this->decorator->setOptions($options);
        foreach ($options as $key => $value) {
            if ($key == 'ignoreSubForms') {
                $this->assertTrue($this->decorator->ignoreSubForms());
            } else {
                $method = 'get' . ucfirst($key);
                $this->assertEquals($value, $this->decorator->$method());
            }
        }
    }

    public function testMarkupOptionsShouldBeUsedWhenRendering()
    {
        $options = [
            'ignoreSubForms' => true,
            'markupElementLabelEnd' => '</i>',
            'markupElementLabelStart' => '<i>',
            'markupListEnd' => '</div>',
            'markupListItemEnd' => '</p>',
            'markupListItemStart' => '<p>',
            'markupListStart' => '<div class="form-errors">',
        ];
        $this->setupForm();
        $this->decorator->setOptions($options);
        $markup = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ($key == 'ignoreSubForms') {
                $this->assertStringNotContainsString('Sub ', $markup);
            } else {
                $this->assertStringContainsString($value, $markup);
            }
        }
    }

    public function testRenderIsArrayForm()
    {
        $this->setupForm();
        $this->form->setName('foo')
            ->setIsArray(true);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content, $test);
        foreach ($this->form->getMessages() as $name => $messages) {
            while (($message = current($messages))) {
                if (is_string($message)) {
                    $this->assertContains($message, $test, var_export($messages, 1));
                }
                if (false === next($messages) && is_array(prev($messages))) {
                    $messages = current($messages);
                }
            }
        }
    }

    public function testCustomFormErrors()
    {
        $this->setupForm();
        $this->form->addDecorator($this->decorator)
            ->addError('form-badness');
        $html = $this->form->render();
        $this->assertStringContainsString('form-badness', $html);

        $this->decorator->setOnlyCustomFormErrors(true);
        $html = $this->form->render();
        $this->assertDoesNotMatchRegularExpression('/form-errors.*?Master Foo/', $html);

        $this->decorator->setShowCustomFormErrors(false);
        $html = $this->form->render();
        $this->assertStringNotContainsString('form-badness', $html);
    }

    /**
     * @dataProvider markupOptionMethodsProvider
     *
     * @param mixed $property
     */
    public function testMarkupOptionsMayBeMutated($property)
    {
        $setter = 'set' . $property;
        $getter = 'get' . $property;

        $this->decorator->$setter('foo');
        if ($property == 'IgnoreSubForms') {
            $this->assertTrue($this->decorator->ignoreSubForms());
        } else {
            $this->assertEquals('foo', $this->decorator->$getter());
        }
    }

    /**
     * @group ZF-11151
     */
    public function testOptionShowCustomFormErrors()
    {
        $this->decorator
            ->setOption('showCustomFormErrors', true);

        $this->assertTrue($this->decorator->getShowCustomFormErrors());
    }

    /**
     * @group ZF-11225
     */
    public function testRenderingEscapesFormErrorsByDefault()
    {
        $this->setupForm();
        $this->form->addDecorator($this->decorator)
            ->addError('<strong>form-badness</strong>');
        $html = $this->form->render();
        $this->assertStringContainsString('&lt;strong&gt;form-badness&lt;/strong&gt;', $html);
    }

    /**
     * @group ZF-11225
     */
    public function testCanDisableEscapingFormErrors()
    {
        $this->setupForm();
        $this->form->addDecorator($this->decorator);

        // Set error message with html content
        $this->form->addError('<strong>form-badness</strong>');

        // Set element label with html content
        $this->form->getElement('bar')->setLabel('<strong>Sub Bar: </strong>');

        $this->form->getDecorator('FormErrors')->setEscape(false);

        $html = $this->form->render();
        $this->assertStringContainsString('<li><strong>form-badness</strong>', $html);
        $this->assertStringContainsString('<li><b><strong>Sub Bar: </strong>', $html);
    }

    /**
     * @group ZF-8713
     */
    public function testElementNameIsTranslated()
    {
        // Translator
        $translator = new Zend_Translate(
            'array',
            [
                'Master Foo: ' => 'transleted label',
                'bar' => 'translated name',
            ]
        );

        // Form
        $this->setupForm();
        $this->form->setDecorators([$this->decorator]);
        $this->form->foo->setTranslator($translator);
        $this->form->bar->setTranslator($translator);

        // Test
        $html = $this->form->render();
        $this->assertStringContainsString(
            '<li><b>transleted label</b><ul class="errors">',
            $html
        );
        $this->assertStringContainsString(
            '<li><b>translated name</b><ul class="errors">',
            $html
        );
    }

    public function markupOptionMethodsProvider()
    {
        return [
            ['IgnoreSubForms'],
            ['MarkupElementLabelEnd'],
            ['MarkupElementLabelStart'],
            ['MarkupListEnd'],
            ['MarkupListItemEnd'],
            ['MarkupListItemStart'],
            ['MarkupListStart'],
        ];
    }
}
