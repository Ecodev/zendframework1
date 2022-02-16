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

namespace Zend\Form\Element\FileTest\Adapter;

use Zend_File_Transfer_Adapter_Abstract;

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Bar extends Zend_File_Transfer_Adapter_Abstract
{
    public $received = false;

    public function send($options = null)
    {
    }

    public function receive($options = null)
    {
        $this->received = true;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
    }
}
