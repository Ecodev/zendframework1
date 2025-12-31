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
require_once 'Zend/View/Helper/FormTextarea.php';

/**
 * Zend_View_Helper_FormTextareaTest.
 *
 * Tests formTextarea helper
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
#[PHPUnit\Framework\Attributes\UsesClass('\PHPUnit\Framework\TestCase')]
class Zend_View_Helper_FormTextareaTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_View_Helper_FormTextareaTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormTextarea();
        $this->helper->setView($this->view);
    }

    /**
     * ZF-1666.
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->formTextarea([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertMatchesRegularExpression('/<textarea[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * ZF-1666.
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->formTextarea([
            'name' => 'foo',
            'value' => 'bar',
            'attribs' => ['disable' => true],
        ]);

        $this->assertDoesNotMatchRegularExpression('/<textarea[^>]*?(type="hidden")/', $html);
    }
}
