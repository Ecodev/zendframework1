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
class Zend_Filter_Word_SeparatorToCamelCase extends Zend_Filter_Word_Separator_Abstract
{
    public function filter($value)
    {
        // a unicode safe way of converting characters to \x00\x00 notation
        $pregQuotedSeparator = preg_quote($this->_separator, '#');

        if (self::isUnicodeSupportEnabled()) {
            parent::setMatchPattern(['#(' . $pregQuotedSeparator . ')(\p{L}{1})#','#(^\p{Ll}{1})#']);
            parent::setReplacement([\Zend_Filter_Word_SeparatorToCamelCase::class, '_strtoupperArray']);
        } else {
            parent::setMatchPattern(['#(' . $pregQuotedSeparator . ')([A-Za-z]{1})#','#(^[A-Za-z]{1})#']);
            parent::setReplacement([\Zend_Filter_Word_SeparatorToCamelCase::class, '_strtoupperArray']);
        }

        return preg_replace_callback($this->_matchPattern, $this->_replacement, $value);
    }

    /**
     * @return string
     */
    private static function _strtoupperArray(array $matches)
    {
        if (array_key_exists(2, $matches)) {
            return strtoupper($matches[2]);
        }

        return strtoupper($matches[1]);
    }
}
