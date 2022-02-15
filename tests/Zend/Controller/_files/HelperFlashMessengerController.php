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
class HelperFlashMessengerController extends Zend_Controller_Action
{
    /**
     * Test Function for indexAction.
     */
    public function indexAction()
    {
        $flashmessenger = $this->_helper->FlashMessenger;
        $this->getResponse()->appendBody(get_class($flashmessenger));

        $messages = $flashmessenger->getCurrentMessages();
        if ((is_countable($messages) ? count($messages) : 0) === 0) {
            $this->getResponse()->appendBody('1');
        }

        $flashmessenger->addMessage('My message');
        $messages = $flashmessenger->getCurrentMessages();

        if (implode('', $messages) === 'My message') {
            $this->getResponse()->appendBody('2');
        }

        if ($flashmessenger->count() === 0) {
            $this->getResponse()->appendBody('3');
        }

        if ($flashmessenger->hasMessages() === false) {
            $this->getResponse()->appendBody('4');
        }

        if ($flashmessenger->getRequest() === $this->getRequest()) {
            $this->getResponse()->appendBody('5');
        }

        if ($flashmessenger->getResponse() === $this->getResponse()) {
            $this->getResponse()->appendBody('6');
        }
    }
}
