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
require_once 'Zend/Form/Decorator/Errors.php';

require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Errors.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Form')]
class Zend_Form_Decorator_ErrorsTest extends PHPUnit\Framework\TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_Errors();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');

        return $view;
    }

    public function setupElement()
    {
        $element = new Zend_Form_Element('foo');
        $element->addValidator('Alnum')
            ->addValidator('Alpha')
            ->setView($this->getView());
        $element->isValid('abc-123');
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderRendersAllErrorMessages()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content, $test);
        foreach ($this->element->getMessages() as $message) {
            $this->assertStringContainsString($message, $test);
        }
    }

    public function testRenderAppendsMessagesToContentByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertMatchesRegularExpression('#' . $content . '.*?<ul#s', $test, $test);
    }

    public function testRenderPrependsMessagesToContentWhenRequested()
    {
        $this->decorator->setOptions(['placement' => 'PREPEND']);
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertMatchesRegularExpression('#</ul>.*?' . $content . '#s', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithPhpEolByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content . PHP_EOL . '<ul', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithCustomSeparatorWhenRequested()
    {
        $this->decorator->setOptions(['separator' => '<br />']);
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertStringContainsString($content . $this->decorator->getSeparator() . '<ul', $test, $test);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-11476?')]
    public function testRenderingWithFormAsElement()
    {
        // Set up form
        $form = new Zend_Form(
            [
                'elements' => [
                    'foo' => new Zend_Form_Element('foo'),
                    'bar' => new Zend_Form_Element('bar'),
                ],
                'view' => $this->getView(),
                'elementsBelongTo' => 'foobar',
            ]
        );

        $this->decorator->setElement($form);

        // Tests
        $this->assertEquals(
            ['foobar' => []],
            $form->getMessages()
        );
        $this->assertEquals(
            [],
            $form->getMessages(null, true)
        );
        $this->assertEquals(
            'test content',
            $this->decorator->render('test content')
        );
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-11476?')]
    public function testRenderingWithSubFormAsElement()
    {
        // Set up sub form
        $subForm = new Zend_Form_SubForm(
            [
                'elements' => [
                    'foo' => new Zend_Form_Element('foo'),
                    'bar' => new Zend_Form_Element('bar'),
                ],
                'view' => $this->getView(),
                'name' => 'foobar',
            ]
        );

        $this->decorator->setElement($subForm);

        // Tests
        $this->assertEquals(
            ['foobar' => []],
            $subForm->getMessages()
        );
        $this->assertEquals(
            [],
            $subForm->getMessages(null, true)
        );
        $this->assertEquals(
            'test content',
            $this->decorator->render('test content')
        );
    }
}
