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
/**
 * Test class for Zend_Form_Element_Image.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_Form')]
class Zend_Form_Element_ImageTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_Form_Element_ImageTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->element = new Zend_Form_Element_Image('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testImageElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testImageElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testImageElementUsesImageDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('Image');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Image);
    }

    /**
     * ZF-2717.
     */
    public function testImageShouldSetHelperPropertyToFormImageByDefault()
    {
        $this->assertEquals('formImage', $this->element->helper);
    }

    public function testImageSourceValueNullByDefault()
    {
        $this->assertNull($this->element->getImage());
        $this->assertNull($this->element->src);
    }

    public function testCanSetImageSourceViaAccessors()
    {
        $this->element->setImage('foo.gif');
        $this->assertEquals('foo.gif', $this->element->getImage());
        $this->assertEquals('foo.gif', $this->element->src);
    }

    public function testImageSourceUsedWhenRenderingImage()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new Zend_View());
        $this->assertStringContainsString('src="foo.gif"', $html);
    }

    public function testHelperAttributeNotRenderedWhenRenderingImage()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new Zend_View());
        $this->assertStringNotContainsString('helper="', $html);
    }

    public function testValueEmptyWhenRenderingImageByDefault()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new Zend_View());
        if (!strstr($html, 'value="')) {
            return;
        }
        $this->assertStringContainsString('value=""', $html);
    }

    public function testLabelUsedAsAltAttribute()
    {
        $this->element->setLabel('Foo Bar');
        $html = $this->element->render(new Zend_View());
        $this->assertMatchesRegularExpression('#<input[^>]*alt="Foo Bar"#', $html);
    }

    public function testImageValueRenderedAsElementValue()
    {
        $this->element->setImageValue('foo')
            ->setImage('foo.gif');
        $html = $this->element->render(new Zend_View());
        $this->assertMatchesRegularExpression('#<input[^>]*value="foo"#', $html, $html);
    }

    public function testIsCheckedReturnsSetValueMatchesImageValue()
    {
        $this->assertFalse($this->element->isChecked());
        $this->element->setImageValue('foo');
        $this->assertFalse($this->element->isChecked());
        $this->element->setValue('foo');
        $this->assertTrue($this->element->isChecked());
        $this->element->setValue('bar');
        $this->assertFalse($this->element->isChecked());
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
     * Prove the fluent interface on Zend_Form_Element_Image::loadDefaultDecorators.
     *
     * @see http://framework.zend.com/issues/browse/ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }
}
