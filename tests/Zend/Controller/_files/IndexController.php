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
require_once 'Zend/Controller/Action.php';

/**
 * Mock file for testbed.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class IndexController extends Zend_Controller_Action
{
    /**
     * Test Function for indexAction.
     */
    public function indexAction()
    {
        $this->_response->appendBody("Index action called\n");
    }

    /**
     * Test Function for prefixAction.
     */
    public function prefixAction()
    {
        $this->_response->appendBody("Prefix action called\n");
    }

    /**
     * Test Function for argsAction.
     */
    public function argsAction()
    {
        $args = '';
        foreach ($this->getInvokeArgs() as $key => $value) {
            $args .= $key . ': ' . $value . '; ';
        }

        $this->_response->appendBody('Args action called with params ' . $args . "\n");
    }

    /**
     * Test Function for replaceAction.
     */
    public function replaceAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index')
            ->setActionName('reset')
            ->setDispatched(false);
        $response = new Zend_Controller_Response_Http();
        $front = Zend_Controller_Front::getInstance();
        $front->setRequest($request)
            ->setResponse($response);
    }

    /**
     * Test Function for resetAction.
     */
    public function resetAction()
    {
        $this->_response->appendBody('Reset action called');
    }
}
