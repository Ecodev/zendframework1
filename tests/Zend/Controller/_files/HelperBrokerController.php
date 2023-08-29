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
class HelperBrokerController extends Zend_Controller_Action
{
    /**
     * Test Function for testGetRedirectorAction.
     */
    public function testGetRedirectorAction()
    {
        $redirector = $this->_helper->getHelper('Redirector');
        $this->getResponse()->appendBody(get_class($redirector));
    }

    /**
     * Test Function for testHelperViaMagicGetAction.
     */
    public function testHelperViaMagicGetAction()
    {
        $redirector = $this->_helper->Redirector;
        $this->getResponse()->appendBody(get_class($redirector));
    }

    /**
     * Test Function for testHelperViaMagicCallAction.
     */
    public function testHelperViaMagicCallAction()
    {
        $this->getResponse()->appendBody($this->_helper->TestHelper());
    }

    /**
     * Test Function for testBadHelperAction.
     */
    public function testBadHelperAction()
    {
        try {
            $this->_helper->getHelper('NonExistentHelper');
        } catch (Exception $e) {
            $this->getResponse()->appendBody($e->getMessage());
        }
    }

    /**
     * Test Function for testCustomHelperAction.
     */
    public function testCustomHelperAction()
    {
        $this->getResponse()->appendBody(get_class($this->_helper->TestHelper));
    }

    public function testCanLoadNamespacedHelperAction()
    {
        try {
            $helper = $this->_helper->getHelper('NamespacedHelper');
            $this->getResponse()->appendBody(get_class($helper));
        } catch (Exception $e) {
            $this->getResponse()->appendBody($e->getMessage());
        }
    }
}
