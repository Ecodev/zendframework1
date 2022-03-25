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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_StringTrim implements Zend_Filter_Interface
{
    /**
     * List of characters provided to the trim() function.
     *
     * If this is null, then trim() is called with no specific character list,
     * and its default behavior will be invoked, trimming whitespace.
     *
     * @var null|string
     */
    protected $_charList;

    /**
     * Sets filter options.
     *
     * @param  array|string|Zend_Config $options
     */
    public function __construct($options = null)
    {
        $temp = [];
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['charlist'] = array_shift($options);
            $options = $temp;
        }

        if (array_key_exists('charlist', $options)) {
            $this->setCharList($options['charlist']);
        }
    }

    /**
     * Returns the charList option.
     *
     * @return null|string
     */
    public function getCharList()
    {
        return $this->_charList;
    }

    /**
     * Sets the charList option.
     *
     * @param  null|string $charList
     *
     * @return Zend_Filter_StringTrim Provides a fluent interface
     */
    public function setCharList($charList)
    {
        $this->_charList = $charList;

        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface.
     *
     * Returns the string $value with characters stripped from the beginning and end
     *
     * @param  string $value
     *
     * @return string
     */
    public function filter($value)
    {
        if (null === $this->_charList) {
            return $this->_unicodeTrim((string) $value);
        }

        return $this->_unicodeTrim((string) $value, $this->_charList);
    }

    /**
     * Unicode aware trim method
     * Fixes a PHP problem.
     *
     * @param string $value
     * @param string $charlist
     *
     * @return string
     */
    protected function _unicodeTrim($value, $charlist = '\\\\s')
    {
        $chars = preg_replace(
            ['/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'],
            ['\\\\\\0', '\\', '\/'],
            $charlist
        );

        $pattern = '^[' . $chars . ']*|[' . $chars . ']*$';

        return preg_replace("/$pattern/sSD", '', $value);
    }
}
