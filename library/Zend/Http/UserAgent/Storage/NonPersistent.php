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
 * @version    $Id: NonPersistent.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * Non-Persistent Browser Storage.
 *
 * Since HTTP Browserentication happens again on each request, this will always be
 * re-populated. So there's no need to use sessions, this simple value class
 * will hold the data for rest of the current request.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Http_UserAgent_Storage_NonPersistent implements Zend_Http_UserAgent_Storage
{
    /**
     * Holds the actual Browser data.
     *
     * @var mixed
     */
    protected $_data;

    /**
     * Returns true if and only if storage is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_data);
    }

    /**
     * Returns the contents of storage.
     *
     * Behavior is undefined when storage is empty.
     *
     * @return mixed
     */
    public function read()
    {
        return $this->_data;
    }

    /**
     * Writes $contents to storage.
     *
     * @param  mixed $contents
     */
    public function write($contents)
    {
        $this->_data = $contents;
    }

    /**
     * Clears contents from storage.
     */
    public function clear()
    {
        $this->_data = null;
    }
}
