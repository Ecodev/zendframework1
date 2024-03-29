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
require_once 'Zend/Form/Element/MultiCheckbox.php';

/**
 * Test class for Zend_Form_Element_MultiCheckbox.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_MultiCheckboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_MultiCheckboxTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_MultiCheckbox('foo');
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

    public function testMultiCheckboxElementSubclassesMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testMultiCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testMultiCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testMultiCheckboxElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testHelperAttributeSetToFormMultiCheckboxByDefault()
    {
        $this->assertEquals('formMultiCheckbox', $this->element->getAttrib('helper'));
    }

    public function testMultiCheckboxElementUsesMultiCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formMultiCheckbox', $helper);
    }

    public function testCanDisableIndividualMultiCheckboxOptions()
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

    /**
     * @group ZF-2830
     */
    public function testRenderingMulticheckboxCreatesCorrectArrayNotation()
    {
        $this->element->addMultiOption(1, 'A');
        $this->element->addMultiOption(2, 'B');
        $html = $this->element->render($this->getView());
        $this->assertStringContainsString('name="foo[]"', $html, $html);
        $count = substr_count($html, 'name="foo[]"');
        $this->assertEquals(2, $count);
    }

    /**
     * @group ZF-2828
     */
    public function testCanPopulateCheckboxOptionsFromPostedData()
    {
        $form = new Zend_Form([
            'elements' => [
                '100_1' => ['MultiCheckbox', [
                    'multiOptions' => [
                        '100_1_1' => 'Agriculture',
                        '100_1_2' => 'Automotive',
                        '100_1_12' => 'Chemical',
                        '100_1_13' => 'Communications',
                    ],
                    'required' => true,
                ]],
            ],
        ]);
        $data = [
            '100_1' => [
                '100_1_1',
                '100_1_2',
                '100_1_12',
                '100_1_13',
            ],
        ];
        $form->populate($data);
        $html = $form->render($this->getView());
        foreach ($form->getElement('100_1')->getMultiOptions() as $key => $value) {
            if (!preg_match('#(<input[^>]*' . $key . '[^>]*>)#', $html, $m)) {
                $this->fail('Missing input for a given multi option: ' . $html);
            }
            $this->assertStringContainsString('checked="checked"', $m[1]);
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

    /**#+
     * @group ZF-3286
     */
    public function testShouldRegisterInArrayValidatorByDefault()
    {
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testShouldAllowSpecifyingWhetherOrNotToUseInArrayValidator()
    {
        $this->testShouldRegisterInArrayValidatorByDefault();
        $this->element->setRegisterInArrayValidator(false);
        $this->assertFalse($this->element->registerInArrayValidator());
        $this->element->setRegisterInArrayValidator(true);
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testInArrayValidatorShouldBeRegisteredAfterValidation()
    {
        $options = [
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        ];
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $validator = $this->element->getValidator('InArray');
        $this->assertTrue($validator instanceof Zend_Validate_InArray);
    }

    public function testShouldNotValidateIfValueIsNotInArray()
    {
        $options = [
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        ];
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->assertFalse($this->element->isValid('test'));
    }
    // #@-

    /**
     * No assertion; just making sure no error occurs.
     *
     * @group ZF-4915
     */
    public function testRetrievingErrorMessagesShouldNotResultInError()
    {
        $this->element->addMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ])
            ->addErrorMessage('%value% is invalid');
        $this->element->isValid(['foo', 'bogus']);
        $html = $this->element->render($this->getView());
        self::assertTrue(true);
    }

    /**
     * @group ZF-11402
     */
    public function testValidateShouldNotAcceptEmptyArray()
    {
        $this->element->addMultiOptions([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ]);
        $this->element->setRegisterInArrayValidator(true);

        $this->assertTrue($this->element->isValid(['foo']));
        $this->assertTrue($this->element->isValid(['foo','baz']));

        $this->element->setAllowEmpty(true);
        $this->assertTrue($this->element->isValid([]));

        // Empty value + AllowEmpty=true = no error messages
        $messages = $this->element->getMessages();
        $this->assertEquals(0, is_countable($messages) ? count($messages) : 0, 'Received unexpected error message(s)');

        $this->element->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid([]));

        // Empty value + AllowEmpty=false = notInArray error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('notInArray', $messages, 'Expected \'notInArray\' error message');

        $this->element->setRequired(true)->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid([]));

        // Empty value + Required=true + AllowEmpty=false = isEmpty error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('isEmpty', $messages, 'Expected \'isEmpty\' error message');
    }

    /**
     * @group ZF-12059
     */
    public function testDisabledForAttribute()
    {
        $this->element->setLabel('Foo');

        $expected = '<dt id="foo-label"><label class="optional">Foo</label></dt>'
                  . PHP_EOL
                  . '<dd id="foo-element">'
                  . PHP_EOL
                  . '</dd>';
        $this->assertSame($expected, $this->element->render($this->getView()));
    }

    /**
     * @group ZF-12059
     */
    public function testDisabledForAttributeWithoutLabelDecorator()
    {
        $this->element->setLabel('Foo')->removeDecorator('label');

        $expected = '<dd id="foo-element">'
                  . PHP_EOL
                  . '</dd>';
        $this->assertSame($expected, $this->element->render($this->getView()));
    }
}
