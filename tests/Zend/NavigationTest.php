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
 * @version    $Id:$
 */

/**
 * Zend_Navigation.
 */
require_once 'Zend/Navigation.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Navigation
 */
class Zend_NavigationTest extends \PHPUnit\Framework\TestCase
{
    private ?\Zend_Navigation $_navigation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_navigation = new Zend_Navigation();
    }

    protected function tearDown(): void
    {
        $this->_navigation = null;
        parent::tearDown();
    }

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_NavigationTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Testing that navigation order is done correctly.
     *
     * @group   ZF-8337
     * @group   ZF-8313
     */
    public function testNavigationArraySortsCorrectly()
    {
        require_once 'Zend/Navigation/Page/Uri.php';
        $page1 = new Zend_Navigation_Page_Uri(['uri' => 'page1']);
        $page2 = new Zend_Navigation_Page_Uri(['uri' => 'page2']);
        $page3 = new Zend_Navigation_Page_Uri(['uri' => 'page3']);

        $this->_navigation->setPages([$page1, $page2, $page3]);

        $page1->setOrder(1);
        $page3->setOrder(0);
        $page2->setOrder(2);

        $pages = $this->_navigation->toArray();
        $this->assertSame(3, count($pages));
        $this->assertEquals('page3', $pages[0]['uri']);
        $this->assertEquals('page1', $pages[1]['uri']);
        $this->assertEquals('page2', $pages[2]['uri']);
    }
}
