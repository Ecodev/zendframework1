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
require_once 'Zend/Form/Element.php';

/**
 * Test class for Zend_Form_Decorator_Callback.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_CallbackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_CallbackTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_Callback();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testCanSetCallback()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());

        $callback = ['Zend_Form_Decorator_CallbackTest_TestCallbackClass', 'direct'];
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testCanSetCallbackViaOptions()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $this->decorator->setOptions(['callback' => $callback]);
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testInvalidCallbackRaisesException()
    {
        try {
            $this->decorator->setCallback(true);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (Zend_Form_Exception $e) {
            $this->assertStringContainsString('Invalid', $e->getMessage());
        }

        try {
            $o = new stdClass();
            $this->decorator->setCallback($o);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (Zend_Form_Exception $e) {
            $this->assertStringContainsString('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(['foo', 'bar', 'baz']);
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (Zend_Form_Exception $e) {
            $this->assertStringContainsString('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(['foo']);
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (Zend_Form_Exception $e) {
            $this->assertStringContainsString('Invalid', $e->getMessage());
        }
    }

    public function testRenderCallsFunctionCallback()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(['callback' => $callback])
            ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertStringContainsString('foo bar', $content);
        $this->assertStringContainsString($element->getName(), $content);
        $this->assertStringContainsString($element->getLabel(), $content);
    }

    public function testRenderCallsMethodCallback()
    {
        $callback = ['Zend_Form_Decorator_CallbackTest_TestCallbackClass', 'direct'];
        $element = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(['callback' => $callback])
            ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertStringContainsString('foo bar', $content);
        $this->assertStringContainsString($element->getName(), $content);
        $this->assertStringContainsString($element->getLabel(), $content);
        $this->assertStringContainsString('Item ', $content);
    }

    public function testRenderCanPrepend()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(['callback' => $callback, 'placement' => 'prepend'])
            ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertStringContainsString('foo bar', $content);
        $this->assertStringContainsString($element->getName(), $content);
        $this->assertStringContainsString($element->getLabel(), $content);
        $this->assertMatchesRegularExpression('/foo bar$/s', $content);
    }

    public function testRenderCanReplaceContent()
    {
        $callback = 'Zend_Form_Decorator_CallbackTest_TestCallback';
        $element = new Zend_Form_Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(['callback' => $callback, 'placement' => false])
            ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertStringNotContainsString('foo bar', $content, $content);
        $this->assertStringContainsString($element->getName(), $content);
        $this->assertStringContainsString($element->getLabel(), $content);
    }
}

function Zend_Form_Decorator_CallbackTest_TestCallback($content, $element, array $options)
{
    $name = $element->getName();
    $label = '';
    if (method_exists($element, 'getLabel')) {
        $label = $element->getLabel();
    }
    $html = <<<EOH
        $label: $name

        EOH;

    return $html;
}

#[AllowDynamicProperties]
class Zend_Form_Decorator_CallbackTest_TestCallbackClass
{
    public static function direct($content, $element, array $options)
    {
        $name = $element->getName();
        $label = '';
        if (method_exists($element, 'getLabel')) {
            $label = $element->getLabel();
        }
        $html = <<<EOH
            Item "$label": $name

            EOH;

        return $html;
    }
}
