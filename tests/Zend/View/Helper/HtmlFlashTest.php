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
require_once 'Zend/View/Helper/HtmlFlash.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_HtmlFlashTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_View_Helper_HtmlFlash
     */
    public $helper;

    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_HtmlFlashTest');
        \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_HtmlFlash();
        $this->helper->setView($this->view);
    }

    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testMakeHtmlFlash()
    {
        $htmlFlash = $this->helper->htmlFlash('/path/to/flash.swf');

        $objectStartElement = '<object data="/path/to/flash.swf" type="application/x-shockwave-flash">';

        $this->assertStringContainsString($objectStartElement, $htmlFlash);
        $this->assertStringContainsString('</object>', $htmlFlash);
    }
}
