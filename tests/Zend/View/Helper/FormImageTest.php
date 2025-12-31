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
require_once 'Zend/View/Helper/FormImage.php';

/**
 * Test class for Zend_View_Helper_FormImage.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
class Zend_View_Helper_FormImageTest extends PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit\Framework\TestSuite('Zend_View_Helper_FormImageTest');
        $result = PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->view = new Zend_View();

        $this->helper = new Zend_View_Helper_FormImage();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testFormImageRendersFormImageXhtml()
    {
        $button = $this->helper->formImage('foo', 'bar');
        $this->assertMatchesRegularExpression('/<input[^>]*?src="bar"/', $button);
        $this->assertMatchesRegularExpression('/<input[^>]*?name="foo"/', $button);
        $this->assertMatchesRegularExpression('/<input[^>]*?type="image"/', $button);
    }

    public function testDisablingFormImageRendersImageInputWithDisableAttribute()
    {
        $button = $this->helper->formImage('foo', 'bar', ['disable' => true]);
        $this->assertMatchesRegularExpression('/<input[^>]*?disabled="disabled"/', $button);
        $this->assertMatchesRegularExpression('/<input[^>]*?src="bar"/', $button);
        $this->assertMatchesRegularExpression('/<input[^>]*?name="foo"/', $button);
        $this->assertMatchesRegularExpression('/<input[^>]*?type="image"/', $button);
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-11477')]
    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->formImage([
            'name' => 'foo',
        ]);
        $this->assertStringNotContainsString(' />', $test);
    }
}
