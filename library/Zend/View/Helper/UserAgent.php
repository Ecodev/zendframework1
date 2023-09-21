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
 */

/** Zend_View_Helper_Abstract */

/**
 * Helper for interacting with UserAgent instance.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_View_Helper_UserAgent extends Zend_View_Helper_Abstract
{
    /**
     * UserAgent instance.
     *
     * @var Zend_Http_UserAgent
     */
    protected $_userAgent;

    /**
     * Helper method: retrieve or set UserAgent instance.
     *
     * @return Zend_Http_UserAgent
     */
    public function userAgent(?Zend_Http_UserAgent $userAgent = null)
    {
        if (null !== $userAgent) {
            $this->setUserAgent($userAgent);
        }

        return $this->getUserAgent();
    }

    /**
     * Set UserAgent instance.
     *
     * @return Zend_View_Helper_UserAgent
     */
    public function setUserAgent(Zend_Http_UserAgent $userAgent)
    {
        $this->_userAgent = $userAgent;

        return $this;
    }

    /**
     * Retrieve UserAgent instance.
     *
     * If none set, instantiates one using no configuration
     *
     * @return Zend_Http_UserAgent
     */
    public function getUserAgent()
    {
        if (null === $this->_userAgent) {
            $this->setUserAgent(new Zend_Http_UserAgent());
        }

        return $this->_userAgent;
    }
}
