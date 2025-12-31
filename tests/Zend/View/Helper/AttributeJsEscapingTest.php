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
 * @version    $Id: FormSubmitTest.php 23772 2011-02-28 21:35:29Z ralph $
 */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HtmlElement JS Escaping.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
class Zend_View_Helper_AttributeJsEscapingTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_View_Helper_FormSubmitTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        if (Zend_Registry::isRegistered(Zend_View_Helper_Doctype::class)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[Zend_View_Helper_Doctype::class]);
        }
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormSubmit();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->helper, $this->view);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-9926')]
    public function testRendersSubmitInput()
    {
        $html = $this->helper->formSubmit([
            'name' => 'foo',
            'value' => 'Submit!',
            'attribs' => ['onsubmit' => ['foo', '\'bar\'', 10]],
        ]);
        $this->assertEquals('<input type="submit" name="foo" id="foo" value="Submit!" onsubmit=\'["foo","&#39;bar&#39;",10]\'>', $html);
    }
}
