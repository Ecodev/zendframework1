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
require_once 'Zend/View/Helper/DeclareVars.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_DeclareVarsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @static
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_DeclareVarsTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function setUp(): void
    {
        $view = new Zend_View();
        $base = str_replace('/', DIRECTORY_SEPARATOR, '/../_templates');
        $view->setScriptPath(__DIR__ . $base);
        $view->strictVars(true);
        $this->view = $view;
    }

    public function tearDown(): void
    {
        unset($this->view);
    }

    protected function _declareVars()
    {
        $this->view->declareVars(
            'varName1',
            'varName2',
            [
                'varName3' => 'defaultValue',
                'varName4' => [],
            ]
        );
    }

    public function testDeclareUndeclaredVars()
    {
        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));

        $this->assertEquals('defaultValue', $this->view->varName3);
        $this->assertEquals([], $this->view->varName4);
    }

    public function testDeclareDeclaredVars()
    {
        $this->view->varName2 = 'alreadySet';
        $this->view->varName3 = 'myValue';
        $this->view->varName5 = 'additionalValue';

        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));
        $this->assertTrue(isset($this->view->varName5));

        $this->assertEquals('alreadySet', $this->view->varName2);
        $this->assertEquals('myValue', $this->view->varName3);
        $this->assertEquals('additionalValue', $this->view->varName5);
    }
}
