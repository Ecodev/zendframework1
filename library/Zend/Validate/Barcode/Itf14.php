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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Validate_Barcode_Itf14 extends Zend_Validate_Barcode_AdapterAbstract
{
    /**
     * Allowed barcode lengths.
     *
     * @var int
     */
    protected $_length = 14;

    /**
     * Allowed barcode characters.
     *
     * @var string
     */
    protected $_characters = '0123456789';

    /**
     * Checksum function.
     *
     * @var string
     */
    protected $_checksum = '_gtin';
}
