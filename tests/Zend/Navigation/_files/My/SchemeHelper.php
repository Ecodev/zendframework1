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
 * @version    $Id: UrlHelper.php 24593 2012-01-05 20:35:02Z matthew $
 */
require_once 'Zend/View/Helper/ServerUrl.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class My_SchemeHelper extends Zend_View_Helper_ServerUrl
{
    public const RETURN_URL = 'spotify:track:2nd6CTjR9zjHGT0QtpfLHe';

    public function serverUrl($requestUri = null)
    {
        return self::RETURN_URL;
    }
}
