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
 * Test class for Zend_Form_Element_Password.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Element_PasswordTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Element_PasswordTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->errors = [];
        $this->element = new Zend_Form_Element_Password('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function testPasswordElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testPasswordElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testHelperAttributeSetToFormPasswordByDefault()
    {
        $this->assertEquals('formPassword', $this->element->getAttrib('helper'));
    }

    public function testPasswordElementUsesPasswordHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formPassword', $helper);
    }

    public function testPasswordValueMaskedByGetMessages()
    {
        $this->element->addValidators([
            'Alpha',
            'Alnum',
        ]);
        $value = 'abc-123';
        $expect = '*******';
        $this->assertFalse($this->element->isValid($value));
        foreach ($this->element->getMessages() as $message) {
            $this->assertStringNotContainsString($value, $message);
            $this->assertStringContainsString($expect, $message, $message);
        }
    }

    public function handleErrors($errno, $errmsg, $errfile, $errline)
    {
        if (!isset($this->errors)) {
            $this->errors = [];
        }
        $this->errors[] = $errmsg;
    }

    /**
     * ZF-2656.
     */
    public function testGetMessagesReturnsEmptyArrayWhenNoMessagesRegistered()
    {
        set_error_handler([$this, 'handleErrors']);
        $messages = $this->element->getMessages();
        restore_error_handler();
        $this->assertSame([], $messages);
        $this->assertTrue(empty($this->errors));
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

    public function testRenderPasswordAttributeShouldDefaultToFalse()
    {
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldAllowSettingRenderPasswordFlag()
    {
        $this->testRenderPasswordAttributeShouldDefaultToFalse();
        $this->element->setRenderPassword(true);
        $this->assertTrue($this->element->renderPassword());
        $this->element->setRenderPassword(false);
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldPassRenderPasswordAttributeToViewHelper()
    {
        $this->element->setValue('foobar')
            ->setView(new Zend_View());
        $test = $this->element->render();
        $this->assertStringContainsString('value=""', $test);

        $this->element->setRenderPassword(true);
        $test = $this->element->render();
        $this->assertStringContainsString('value="foobar"', $test);
    }
}
