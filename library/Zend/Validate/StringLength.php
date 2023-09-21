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
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_Validate_StringLength extends Zend_Validate_Abstract
{
    public const INVALID = 'stringLengthInvalid';
    public const TOO_SHORT = 'stringLengthTooShort';
    public const TOO_LONG = 'stringLengthTooLong';

    /**
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID => 'Invalid type given. String expected',
        self::TOO_SHORT => "'%value%' is less than %min% characters long",
        self::TOO_LONG => "'%value%' is more than %max% characters long",
    ];

    /**
     * @var array
     */
    protected $_messageVariables = [
        'min' => '_min',
        'max' => '_max',
    ];

    /**
     * Minimum length.
     *
     * @var int
     */
    protected $_min;

    /**
     * Maximum length.
     *
     * If null, there is no maximum length
     *
     * @var null|int
     */
    protected $_max;

    /**
     * Encoding to use.
     *
     * @var null|string
     */
    protected $_encoding;

    /**
     * Sets validator options.
     *
     * @param array|int|Zend_Config $options
     */
    public function __construct($options = [])
    {
        $temp = [];
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['min'] = array_shift($options);
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['encoding'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            $options['min'] = 0;
        }

        $this->setMin($options['min']);
        if (array_key_exists('max', $options)) {
            $this->setMax($options['max']);
        }

        if (array_key_exists('encoding', $options)) {
            $this->setEncoding($options['encoding']);
        }
    }

    /**
     * Returns the min option.
     *
     * @return int
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option.
     *
     * @param  int $min
     *
     * @return Zend_Validate_StringLength Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->_max && $min > $this->_max) {
            throw new Zend_Validate_Exception("The minimum must be less than or equal to the maximum length, but $min >"
                                . " $this->_max");
        }
        $this->_min = max(0, (integer) $min);

        return $this;
    }

    /**
     * Returns the max option.
     *
     * @return null|int
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option.
     *
     * @param  null|int $max
     *
     * @return Zend_Validate_StringLength Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->_max = null;
        } elseif ($max < $this->_min) {
            throw new Zend_Validate_Exception('The maximum must be greater than or equal to the minimum length, but '
                                . "$max < $this->_min");
        } else {
            $this->_max = (integer) $max;
        }

        return $this;
    }

    /**
     * Returns the actual encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Sets a new encoding to use.
     *
     * @param string $encoding
     *
     * @return Zend_Validate_StringLength
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            $orig = PHP_VERSION_ID < 50600
                        ? iconv_get_encoding('internal_encoding')
                        : ini_get('default_charset');
            if (PHP_VERSION_ID < 50600) {
                if ($encoding) {
                    $result = iconv_set_encoding('internal_encoding', $encoding);
                } else {
                    $result = false;
                }
            } else {
                ini_set('default_charset', $encoding);
                $result = ini_get('default_charset');
            }
            if (!$result) {
                throw new Zend_Validate_Exception('Given encoding not supported on this OS!');
            }

            if (PHP_VERSION_ID < 50600) {
                iconv_set_encoding('internal_encoding', $orig);
            } else {
                ini_set('default_charset', $orig);
            }
        }
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);

            return false;
        }

        $this->_setValue($value);
        if ($this->_encoding !== null) {
            $length = iconv_strlen($value, $this->_encoding);
        } else {
            $length = iconv_strlen($value);
        }

        if ($length < $this->_min) {
            $this->_error(self::TOO_SHORT);
        }

        if (null !== $this->_max && $this->_max < $length) {
            $this->_error(self::TOO_LONG);
        }

        if (count($this->_messages)) {
            return false;
        }

        return true;
    }
}
