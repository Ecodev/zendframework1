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

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Http_UserAgent_Storage
{
    /**
     * Returns true if and only if storage is empty.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Returns the contents of storage associated to the key parameter.
     *
     * Behavior is undefined when storage is empty.
     *
     * @return mixed
     */
    public function read();

    /**
     * Writes $contents associated to the key parameter to storage.
     *
     * @param  mixed $contents
     */
    public function write($contents);

    /**
     * Clears contents from storage.
     */
    public function clear();
}
