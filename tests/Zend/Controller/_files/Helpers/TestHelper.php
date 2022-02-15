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

/**
 * Mock file for testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MyApp_TestHelper extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Test Function for direct.
     */
    public function direct()
    {
        $this->getResponse()->appendBody('running direct call');
    }
}
