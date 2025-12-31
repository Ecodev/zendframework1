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
 * Zend_View_Helper_FormRadioTest.
 *
 * Tests formRadio helper
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
class Zend_View_Helper_FormRadioTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_View_Helper_FormRadioTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormRadio();
        $this->helper->setView($this->view);
    }

    public function testRendersRadioLabelsWhenRenderingMultipleOptions()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
        ]);
        foreach ($options as $key => $value) {
            $this->assertMatchesRegularExpression('#<label.*?>.*?' . $value . '.*?</label>#', $html, $html);
            $this->assertMatchesRegularExpression('#<label.*?>.*?<input.*?</label>#', $html, $html);
        }
    }

    public function testCanSpecifyRadioLabelPlacement()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['labelPlacement' => 'append'],
        ]);
        foreach ($options as $key => $value) {
            $this->assertMatchesRegularExpression('#<label.*?>.*?<input .*?' . $value . '</label>#', $html, $html);
        }

        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['labelPlacement' => 'prepend'],
        ]);
        foreach ($options as $key => $value) {
            $this->assertMatchesRegularExpression('#<label.*?>' . $value . '<input .*?</label>#', $html, $html);
        }
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-3206')]
    public function testSpecifyingLabelPlacementShouldNotOverwriteValue()
    {
        $options = [
            'bar' => 'Bar',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => [
                'labelPlacement' => 'append',
            ],
        ]);
        $this->assertMatchesRegularExpression('#<input[^>]*(checked="checked")#', $html, $html);
    }

    public function testCanSpecifyRadioLabelAttribs()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['labelClass' => 'testclass', 'label_id' => 'testid'],
        ]);

        foreach ($options as $key => $value) {
            $this->assertMatchesRegularExpression('#<label[^>]*?class="testclass"[^>]*>.*?' . $value . '#', $html, $html);
            $this->assertMatchesRegularExpression('#<label[^>]*?id="testid"[^>]*>.*?' . $value . '#', $html, $html);
        }
    }

    public function testCanSpecifyRadioSeparator()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'listsep' => '--FunkySep--',
        ]);

        $this->assertStringContainsString('--FunkySep--', $html);
        $count = substr_count($html, '--FunkySep--');
        $this->assertEquals(2, $count);
    }

    /**
     * ZF-2513.
     */
    public function testCanDisableAllRadios()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['disable' => true],
        ]);

        $this->assertMatchesRegularExpression('/<input[^>]*?(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(3, $count);
    }

    /**
     * ZF-2513.
     */
    public function testCanDisableIndividualRadios()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['disable' => ['bar']],
        ]);

        $this->assertMatchesRegularExpression('/<input[^>]*?(value="bar")[^>]*(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(1, $count);
    }

    /**
     * ZF-2513.
     */
    public function testCanDisableMultipleRadios()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
            'attribs' => ['disable' => ['foo', 'baz']],
        ]);

        foreach (['foo', 'baz'] as $test) {
            $this->assertMatchesRegularExpression('/<input[^>]*?(value="' . $test . '")[^>]*?(disabled="disabled")/', $html, $html);
        }
        $this->assertDoesNotMatchRegularExpression('/<input[^>]*?(value="bar")[^>]*?(disabled="disabled")/', $html, $html);
        $count = substr_count($html, 'disabled="disabled"');
        $this->assertEquals(2, $count);
    }

    public function testLabelsAreEscapedByDefault()
    {
        $options = [
            'bar' => '<b>Bar</b>',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'options' => $options,
        ]);

        $this->assertStringNotContainsString($options['bar'], $html);
        $this->assertStringContainsString('&lt;b&gt;Bar&lt;/b&gt;', $html);
    }

    public function testXhtmlLabelsAreAllowed()
    {
        $options = [
            'bar' => '<b>Bar</b>',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'options' => $options,
            'attribs' => ['escape' => false],
        ]);

        $this->assertStringContainsString($options['bar'], $html);
    }

    /**
     * ZF-1666.
     */
    public function testDoesNotRenderHiddenElements()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'options' => $options,
        ]);

        $this->assertDoesNotMatchRegularExpression('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testSpecifyingAValueThatMatchesAnOptionChecksIt()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
        ]);

        if (!preg_match('/(<input[^>]*?(value="bar")[^>]*>)/', $html, $matches)) {
            $this->fail('Radio for a given option was not found?');
        }
        $this->assertStringContainsString('checked="checked"', $matches[1], var_export($matches, 1));
    }

    public function testOptionsWithMatchesInAnArrayOfValuesAreChecked()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => ['foo', 'baz'],
            'options' => $options,
        ]);

        foreach (['foo', 'baz'] as $value) {
            if (!preg_match('/(<input[^>]*?(value="' . $value . '")[^>]*>)/', $html, $matches)) {
                $this->fail('Radio for a given option was not found?');
            }
            $this->assertStringContainsString('checked="checked"', $matches[1], var_export($matches, 1));
        }
    }

    public function testEachRadioShouldHaveIdCreatedByAppendingFilteredValue()
    {
        $options = [
            'foo bar' => 'Foo',
            'bar baz' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo[]',
            'value' => 'bar',
            'options' => $options,
        ]);

        $filter = new Zend_Filter_Alnum();
        foreach ($options as $key => $value) {
            $id = 'foo-' . $filter->filter($key);
            $this->assertMatchesRegularExpression('/<input([^>]*)(id="' . $id . '")/', $html);
        }
    }

    public function testEachRadioShouldUseAttributeIdWhenSpecified()
    {
        $options = [
            'foo bar' => 'Foo',
            'bar baz' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo[bar]',
            'value' => 'bar',
            'attribs' => ['id' => 'foo-bar'],
            'options' => $options,
        ]);

        $filter = new Zend_Filter_Alnum();
        foreach ($options as $key => $value) {
            $id = 'foo-bar-' . $filter->filter($key);
            $this->assertMatchesRegularExpression('/<input([^>]*)(id="' . $id . '")/', $html);
        }
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-5681')]
    public function testRadioLabelDoesNotContainHardCodedStyle()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
        ]);
        $this->assertStringNotContainsString('style="white-space: nowrap;"', $html);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-8709')]
    public function testRadioLabelContainsNotForAttributeTag()
    {
        $actual = $this->helper->formRadio(
            [
                'name' => 'foo',
                'options' => [
                    'bar' => 'Bar',
                    'baz' => 'Baz',
                ],
            ]
        );

        $expected = '<label><input type="radio" name="foo" id="foo-bar" value="bar">Bar</label><br>'
                  . "\n"
                  . '<label><input type="radio" name="foo" id="foo-baz" value="baz">Baz</label>';

        $this->assertSame($expected, $actual);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-4191')]
    public function testDashesShouldNotBeFilteredFromId()
    {
        $name = 'Foo';
        $options = [
            -1 => 'Test -1',
            0 => 'Test 0',
            1 => 'Test 1',
        ];

        $formRadio = new Zend_View_Helper_FormRadio();
        $formRadio->setView(new Zend_View());
        $html = $formRadio->formRadio($name, -1, null, $options);
        foreach ($options as $key => $value) {
            $fid = "{$name}-{$key}";
            $this->assertMatchesRegularExpression('/<input([^>]*)(id="' . $fid . '")/', $html);
        }

        // Assert that radio for value -1 is the selected one
        $this->assertMatchesRegularExpression('/<input([^>]*)(id="' . $name . '--1")([^>]*)(checked="checked")/', $html);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-11477')]
    public function testRendersAsHtmlByDefault()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formRadio([
            'name' => 'foo',
            'options' => $options,
        ]);

        $this->assertStringContainsString('value="foo">', $html);
        $this->assertStringContainsString('value="bar">', $html);
        $this->assertStringContainsString('value="baz">', $html);
    }
}
