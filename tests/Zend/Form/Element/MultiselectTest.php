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
require_once 'Zend/Translate.php';

/**
 * Test class for Zend_Form_Element_Multiselect.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_MultiselectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_MultiselectTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * @var Zend_Form_Element_Multiselect
     */
    public $element;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_Multiselect('foo');
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
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper/');

        return $view;
    }

    public function testMultiselectElementInstanceOfMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testMultiselectElementInstanceOfXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testMultiselectElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testMultiselectElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testMultiselectElementUsesSelectHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formSelect', $helper);
    }

    public function testMultipleOptionSetByDefault()
    {
        $this->assertNotNull($this->element->multiple);
        $this->assertEquals('multiple', $this->element->multiple);
    }

    public function testHasDefaultSeparator()
    {
        $this->assertEquals('<br />', $this->element->getSeparator());
    }

    public function testCanSetSeparator()
    {
        $this->testHasDefaultSeparator();
        $this->element->setSeparator("\n");
        $this->assertEquals("\n", $this->element->getSeparator());
    }

    public function testMultiOptionsEmptyByDefault()
    {
        $options = $this->element->getMultiOptions();
        $this->assertTrue(is_array($options));
        $this->assertTrue(empty($options));
    }

    public function testCanSetMultiOptions()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->setMultiOptions(['bar' => 'barvalue', 'baz' => 'bazvalue']);
        $this->assertEquals(['bar' => 'barvalue', 'baz' => 'bazvalue'], $this->element->getMultiOptions());
        $this->element->addMultiOptions(['bat' => 'batvalue', 'foo' => 'foovalue']);
        $this->assertEquals(['bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue'], $this->element->getMultiOptions());
        $this->element->addMultiOption('test', 'testvalue');
        $this->assertEquals(['bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue', 'test' => 'testvalue'], $this->element->getMultiOptions());
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingAssocArraysWithKeyValueKeys()
    {
        $options = [
            [
                'value' => '1',
                'key' => 'aa',
            ],
            [
                'key' => '2',
                'value' => 'xxxx',
            ],
            [
                'value' => '444',
                'key' => 'ssss',
            ],
        ];
        $this->element->addMultiOptions($options);
        $this->assertEquals($options[0]['value'], $this->element->getMultiOption('aa'));
        $this->assertEquals($options[1]['value'], $this->element->getMultiOption(2));
        $this->assertEquals($options[2]['value'], $this->element->getMultiOption('ssss'));
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingConfigWithKeyValueKeys()
    {
        $config = new Zend_Config_Xml(__DIR__ . '/../_files/config/multiOptions.xml', 'testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));

        $config = new Zend_Config_Ini(__DIR__ . '/../_files/config/multiOptions.ini', 'testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));
    }

    public function testCanRemoveMultiOption()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->removeMultiOption('foo');
        $this->assertNull($this->element->getMultiOption('foo'));
    }

    public function testOptionsAreRenderedInFinalMarkup()
    {
        $options = [
            'foovalue' => 'Foo',
            'barvalue' => 'Bar',
        ];
        $this->element->addMultiOptions($options);
        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertMatchesRegularExpression('/<option.*value="' . $value . '"[^>]*>' . $label . '/s', $html, $html);
        }
    }

    public function testTranslatedOptionsAreRenderedInFinalMarkupWhenTranslatorPresent()
    {
        $translations = [
            'ThisShouldNotShow' => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value',
        ];
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = [
            'foovalue' => 'ThisShouldNotShow',
            'barvalue' => 'ThisShouldNeverShow',
        ];

        $this->element->setTranslator($translate)
            ->addMultiOptions($options);

        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertStringNotContainsString($label, $html, $html);
            $this->assertMatchesRegularExpression('/<option.*value="' . $value . '"[^>]*>' . $translations[$label] . '/s', $html, $html);
        }
    }

    public function testOptionLabelsAreTranslatedWhenTranslateAdapterIsPresent()
    {
        $translations = include __DIR__ . '/../_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = [
            'foovalue' => 'Foo',
            'barvalue' => 'Bar',
        ];
        $this->element->addMultiOptions($options)
            ->setTranslator($translate);
        $test = $this->element->getMultiOption('barvalue');
        $this->assertEquals($translations[$options['barvalue']], $test);

        $test = $this->element->getMultiOptions();
        foreach ($test as $key => $value) {
            $this->assertEquals($translations[$options[$key]], $value);
        }
    }

    public function testOptionLabelsAreUntouchedIfTranslatonDoesNotExistInnTranslateAdapter()
    {
        $translations = include __DIR__ . '/../_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = [
            'foovalue' => 'Foo',
            'barvalue' => 'Bar',
            'testing' => 'Test Value',
        ];
        $this->element->addMultiOptions($options)
            ->setTranslator($translate);
        $test = $this->element->getMultiOption('testing');
        $this->assertEquals($options['testing'], $test);
    }

    public function testMultiselectIsArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    /**
     * @group ZF-5568
     */
    public function testOptGroupTranslationsShouldWorkAfterPopulatingElement()
    {
        $translations = [
            'ThisIsTheLabel' => 'Optgroup label',
            'ThisShouldNotShow' => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value',
        ];
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = [
            'ThisIsTheLabel' => [
                'foovalue' => 'ThisShouldNotShow',
                'barvalue' => 'ThisShouldNeverShow',
            ],
        ];

        $this->element->setTranslator($translate)
            ->addMultiOptions($options);

        $this->element->setValue('barValue');

        $html = $this->element->render($this->getView());
        $this->assertStringContainsString($translations['ThisIsTheLabel'], $html, $html);
    }

    /**
     * @group ZF-5937
     */
    public function testAddMultiOptionShouldWorkAfterTranslatorIsDisabled()
    {
        $options = [
            'foovalue' => 'Foo',
        ];
        $this->element->setDisableTranslator(true)
            ->addMultiOptions($options);
        $test = $this->element->getMultiOption('foovalue');
        $this->assertEquals($options['foovalue'], $test);
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
     * @group ZF-11667
     */
    public function testSimilarErrorMessagesForMultiElementAreNotDuplicated()
    {
        $this->element->setConcatJustValuesInErrorMessage(true);

        // create element with 4 checkboxes
        $this->element->setMultiOptions([
            'multiOptions' => [
                ['key' => 'a', 'value' => 'A'],
                ['key' => 'b', 'value' => 'B'],
                ['key' => 'c', 'value' => 'C'],
                ['key' => 'd', 'value' => 'D'],
            ],
        ]);

        // check 3 of them
        $this->element->setValue(['A', 'B', 'D']);

        // later on, fails some validation on submit
        $this->element->addError('some error! %value%');

        $this->assertEquals(
            ['some error! A; B; D'],
            $this->element->getMessages()
        );
    }
}
