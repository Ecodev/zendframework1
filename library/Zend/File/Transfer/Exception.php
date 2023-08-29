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
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version   $Id$
 */

/**
 * Zend_Exception.
 */
require_once 'Zend/Exception.php';

/**
 * Exception class for Zend_File_Transfer.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_File_Transfer_Exception extends Zend_Exception
{
    protected $_fileerror;

    public function __construct($message, $fileerror = 0)
    {
        $this->_fileerror = $fileerror;
        parent::__construct($message);
    }

    /**
     * Returns the transfer error code for the exception
     * This is not the exception code !!!
     *
     * @return int
     */
    public function getFileError()
    {
        return $this->_fileerror;
    }
}
