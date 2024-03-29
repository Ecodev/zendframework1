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
abstract class Zend_View_Helper_Abstract implements Zend_View_Helper_Interface
{
    /**
     * View object.
     *
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * Set the View object.
     *
     * @return Zend_View_Helper_Abstract
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Strategy pattern: currently unutilized.
     */
    public function direct()
    {
    }
}
