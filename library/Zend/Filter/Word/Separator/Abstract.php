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
 * @uses       Zend_Filter_PregReplace
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
abstract class Zend_Filter_Word_Separator_Abstract extends Zend_Filter_PregReplace
{
    protected $_separator;

    /**
     * Constructor.
     *
     * @param  string $separator Space by default
     */
    public function __construct($separator = ' ')
    {
        $this->setSeparator($separator);
    }

    /**
     * Sets a new seperator.
     *
     * @param  string  $separator  Seperator
     *
     * @return $this
     */
    public function setSeparator($separator)
    {
        if ($separator == null) {
            throw new Zend_Filter_Exception('"' . $separator . '" is not a valid separator.');
        }
        $this->_separator = $separator;

        return $this;
    }

    /**
     * Returns the actual set seperator.
     *
     * @return  string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }
}
