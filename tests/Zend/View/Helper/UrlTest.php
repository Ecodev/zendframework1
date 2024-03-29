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
 * @version $Id$
 */
require_once 'Zend/View/Helper/Url.php';

// Test dependency on Front Controller because there is no way to get the Controller out of View instance dynamically
require_once 'Zend/Controller/Front.php';

require_once 'Zend/Controller/Request/Http.php';

/**
 * Zend_View_Helper_UrlTest.
 *
 * Tests formText helper, including some common functionality of all form helpers
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_UrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_UrlTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->getRouter()->addDefaultRoutes();

        // $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_Url();
        // $this->helper->setView($this->view);
    }

    public function testDefaultEmpty()
    {
        $url = $this->helper->url();
        $this->assertEquals('/', $url);
    }

    public function testDefault()
    {
        $url = $this->helper->url(['controller' => 'ctrl', 'action' => 'act']);
        $this->assertEquals('/ctrl/act', $url);
    }
}
