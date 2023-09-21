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
 * Simplify AJAX context switching based on requested format.
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Controller_Action_Helper_AjaxContext extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * Controller property to utilize for context switching.
     *
     * @var string
     */
    protected $_contextKey = 'ajaxable';

    /**
     * Constructor.
     *
     * Add HTML context
     */
    public function __construct()
    {
        parent::__construct();
        $this->addContext('html', ['suffix' => 'ajax']);
    }

    /**
     * Initialize AJAX context switching.
     *
     * Checks for XHR requests; if detected, attempts to perform context switch.
     *
     * @param  string $format
     */
    public function initContext($format = null)
    {
        $this->_currentContext = null;

        $request = $this->getRequest();
        if (!method_exists($request, 'isXmlHttpRequest')
            || !$this->getRequest()->isXmlHttpRequest()) {
            return;
        }

        return parent::initContext($format);
    }
}
