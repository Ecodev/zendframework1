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
require_once 'Zend/Controller/Action/Helper/Url.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class My_UrlHelper extends Zend_Controller_Action_Helper_Url
{
    public const RETURN_URL = 'spotify:track:2nd6CTjR9zjHGT0QtpfLHe';

    public function url($urlOptions = [], $name = null, $reset = false, $encode = true)
    {
        return self::RETURN_URL;
    }
}
