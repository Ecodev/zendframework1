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
 * Mock file for testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ObController extends Zend_Controller_Action
{
    /**
     * Test Function for indexAction.
     */
    public function indexAction()
    {
        echo "OB index action called\n";
    }

    /**
     * Test Function for exceptionAction.
     */
    public function exceptionAction()
    {
        echo "In exception action\n";
        $view = new Zend_View();
        $view->addBasePath(dirname(__FILE__, 2) . '/views');
        $view->render('ob.phtml');
    }
}
