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
 * @version    $Id$
 */

/**
 * A simple container class for {@link Zend_Navigation_Page} pages.
 *
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Navigation extends Zend_Navigation_Container
{
    /**
     * Creates a new navigation container.
     *
     * @param array|Zend_Config $pages    [optional] pages to add
     */
    public function __construct($pages = null)
    {
        if (is_array($pages) || $pages instanceof Zend_Config) {
            $this->addPages($pages);
        } elseif (null !== $pages) {
            throw new Zend_Navigation_Exception(
                'Invalid argument: $pages must be an array, an '
                . 'instance of Zend_Config, or null');
        }
    }
}
