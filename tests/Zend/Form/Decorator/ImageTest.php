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
require_once 'Zend/Form/Decorator/Image.php';

require_once 'Zend/Form.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Image.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Image.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_ImageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_ImageTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_Image();
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

    public function testPlacementInitiallyAppends()
    {
        $this->assertEquals(Zend_Form_Decorator_Abstract::APPEND, $this->decorator->getPlacement());
    }

    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testTagInitiallyNull()
    {
        $this->assertNull($this->decorator->getTag());
    }

    public function testCanSetTag()
    {
        $this->testTagInitiallyNull();
        $this->decorator->setTag('div');
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testCanSetTagViaOptions()
    {
        $this->testTagInitiallyNull();
        $this->decorator->setOption('tag', 'div');
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testRendersXhtmlImageTag()
    {
        $element = new Zend_Form_Element_Image('foo');
        $element->setImage('foobar')
            ->setView($this->getView());
        $this->decorator->setElement($element);

        $image = $this->decorator->render('');
        $this->assertContains('<input', $image, $image);
        $this->assertContains('src="foobar"', $image);
        $this->assertContains('name="foo"', $image);
        $this->assertContains('type="image"', $image);
    }

    public function testCanRenderImageWithinAdditionalTag()
    {
        $element = new Zend_Form_Element_Image('foo');
        $element->setValue('foobar')
            ->setView($this->getView());
        $this->decorator->setElement($element)
            ->setOption('tag', 'div');

        $image = $this->decorator->render('');
        $this->assertRegexp('#<div>.*?<input[^>]*>.*?</div>#s', $image, $image);
    }

    public function testCanPrependImageToContent()
    {
        $element = new Zend_Form_Element_Image('foo');
        $element->setValue('foobar')
            ->setView($this->getView());
        $this->decorator->setElement($element)
            ->setOption('placement', 'prepend');

        $image = $this->decorator->render('content');
        $this->assertRegexp('#<input[^>]*>.*?(content)#s', $image, $image);
    }

    /**
     * @group ZF-2714
     */
    public function testImageElementAttributesPassedWithDecoratorOptionsToViewHelper()
    {
        $element = new Zend_Form_Element_Image('foo');
        $element->setValue('foobar')
            ->setAttrib('onClick', 'foo()')
            ->setAttrib('id', 'foo-element')
            ->setView($this->getView());
        $this->decorator->setElement($element)
            ->setOption('class', 'imageclass');

        $image = $this->decorator->render('');
        $this->assertContains('class="imageclass"', $image);
        $this->assertContains('onClick="foo()"', $image);
        $this->assertContains('id="foo-element"', $image);
    }
}
