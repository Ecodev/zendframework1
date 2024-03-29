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

require_once 'Zend/Loader/PluginLoader.php';

/**
 * Test class for Zend_Form_Decorator_Abstract.
 *
 * Uses Zend_Form_Decorator_Errors as a concrete implementation
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_AbstractTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_AbstractTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

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

    public function getOptions()
    {
        $options = [
            'foo' => 'fooval',
            'bar' => 'barval',
            'baz' => 'bazval',
        ];

        return $options;
    }

    public function testCanSetOptions()
    {
        $options = $this->getOptions();
        $this->decorator->setOptions($options);
        $this->assertEquals($options, $this->decorator->getOptions());
    }

    public function testCanSetOptionsFromConfigObject()
    {
        $config = new Zend_Config($this->getOptions());
        $this->decorator->setConfig($config);
        $this->assertEquals($config->toArray(), $this->decorator->getOptions());
    }

    public function testSetElementAllowsFormElements()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $this->assertSame($element, $this->decorator->getElement());
    }

    public function testSetElementAllowsForms()
    {
        $form = new Zend_Form();
        $this->decorator->setElement($form);
        $this->assertSame($form, $this->decorator->getElement());
    }

    public function testSetElementAllowsDisplayGroups()
    {
        $loader = new Zend_Loader_PluginLoader(['Zend_Form_Decorator' => 'Zend/Form/Decorator']);
        $group = new Zend_Form_DisplayGroup('foo', $loader);
        $this->decorator->setElement($group);
        $this->assertSame($group, $this->decorator->getElement());
    }

    public function testSetElementThrowsExceptionWithInvalidElementTypes()
    {
        $config = new Zend_Config([]);

        try {
            $this->decorator->setElement($config);
            $this->fail('Invalid element type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertStringContainsString('Invalid element', $e->getMessage());
        }
    }

    public function testPlacementDefaultsToAppend()
    {
        $this->assertEquals(Zend_Form_Decorator_Abstract::APPEND, $this->decorator->getPlacement());
    }

    public function testCanSetPlacementViaPlacementOption()
    {
        $this->testPlacementDefaultsToAppend();
        $this->decorator->setOptions(['placement' => 'PREPEND']);
        $this->assertEquals(Zend_Form_Decorator_Abstract::PREPEND, $this->decorator->getPlacement());
    }

    public function testSeparatorDefaultsToPhpEol()
    {
        $this->assertEquals(PHP_EOL, $this->decorator->getSeparator());
    }

    public function testCanSetSeparatorViaSeparatorOption()
    {
        $this->testSeparatorDefaultsToPhpEol();
        $this->decorator->setOptions(['separator' => '<br />']);
        $this->assertEquals('<br />', $this->decorator->getSeparator());
    }

    public function testCanSetIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
    }

    public function testCanRemoveIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
        $this->decorator->removeOption('foo');
        $this->assertNull($this->decorator->getOption('foo'));
    }

    public function testCanClearAllOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->assertNull($this->decorator->getOption('bar'));
        $this->assertNull($this->decorator->getOption('baz'));
        $options = ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'];
        $this->decorator->setOptions($options);
        $received = $this->decorator->getOptions();
        $this->assertEquals($options, $received);
        $this->decorator->clearOptions();
        $this->assertEquals([], $this->decorator->getOptions());
    }
}
