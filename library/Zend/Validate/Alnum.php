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
class Zend_Validate_Alnum extends Zend_Validate_Abstract
{
    public const INVALID = 'alnumInvalid';
    public const NOT_ALNUM = 'notAlnum';
    public const STRING_EMPTY = 'alnumStringEmpty';

    /**
     * Whether to allow white space characters; off by default.
     *
     * @var bool
     *
     * @deprecated
     */
    public $allowWhiteSpace;

    /**
     * Alphanumeric filter used for validation.
     *
     * @var Zend_Filter_Alnum
     */
    protected static $_filter;

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID => 'Invalid type given. String, integer or float expected',
        self::NOT_ALNUM => "'%value%' contains characters which are non alphabetic and no digits",
        self::STRING_EMPTY => "'%value%' is an empty string",
    ];

    /**
     * Sets default option values for this instance.
     *
     * @param bool|Zend_Config $allowWhiteSpace
     */
    public function __construct($allowWhiteSpace = false)
    {
        if ($allowWhiteSpace instanceof Zend_Config) {
            $allowWhiteSpace = $allowWhiteSpace->toArray();
        }

        if (is_array($allowWhiteSpace)) {
            if (array_key_exists('allowWhiteSpace', $allowWhiteSpace)) {
                $allowWhiteSpace = $allowWhiteSpace['allowWhiteSpace'];
            } else {
                $allowWhiteSpace = false;
            }
        }

        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
    }

    /**
     * Returns the allowWhiteSpace option.
     *
     * @return bool
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Sets the allowWhiteSpace option.
     *
     * @param bool $allowWhiteSpace
     *
     * @return Zend_Filter_Alnum Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Returns true if and only if $value contains only alphabetic and digit characters
     *
     * @param  string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);

            return false;
        }

        $this->_setValue($value);

        if ('' === $value) {
            $this->_error(self::STRING_EMPTY);

            return false;
        }

        if (null === self::$_filter) {
            // @see Zend_Filter_Alnum
            self::$_filter = new Zend_Filter_Alnum();
        }

        self::$_filter->allowWhiteSpace = $this->allowWhiteSpace;

        if ($value != self::$_filter->filter($value)) {
            $this->_error(self::NOT_ALNUM);

            return false;
        }

        return true;
    }
}
