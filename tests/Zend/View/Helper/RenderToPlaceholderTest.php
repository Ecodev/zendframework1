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
require_once 'Zend/View/Helper/Placeholder.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_RenderToPlaceholderTest extends \PHPUnit\Framework\TestCase
{
    protected $_view;

    public function setUp(): void
    {
        $this->_view = new Zend_View(['scriptPath' => __DIR__ . '/_files/scripts/']);
    }

    public function testDefaultEmpty()
    {
        $this->_view->renderToPlaceholder('rendertoplaceholderscript.phtml', 'fooPlaceholder');
        $placeholder = new Zend_View_Helper_Placeholder();
        $this->assertEquals("Foo Bar\n", $placeholder->placeholder('fooPlaceholder')->getValue());
    }
}
