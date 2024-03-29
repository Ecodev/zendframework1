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
 * Test class for Zend_Controller_Action_Helper_Abstract.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
#[AllowDynamicProperties]
class Zend_Controller_Action_Helper_NamespaceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Controller_Action_Helper_NamespaceTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * @group ZF-10158
     */
    public function testGetNameWithNamespace()
    {
        if (version_compare(PHP_VERSION, '5.3.0') === -1) {
            $this->markTestSkipped('Namespaces not available in PHP < 5.3.0');
        }

        require_once __DIR__ . '/../../_files/Helpers/NamespacedHelper.php';

        $className = 'MyApp\Controller\Action\Helper\NamespacedHelper';
        $helper = new $className();
        $this->assertEquals('NamespacedHelper', $helper->getName());
    }
}

// Call Zend_Controller_Action_Helper_NamespaceTest::main() if this source file is executed directly.
