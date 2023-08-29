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
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zf4207 extends ArrayObject implements Zend_Paginator_Adapter_Interface
{
    public function count(): int
    {
        return 10;
    }

    public function getItems($pageNumber, $itemCountPerPage)
    {
        return new ArrayObject(range(1, 10));
    }
}
