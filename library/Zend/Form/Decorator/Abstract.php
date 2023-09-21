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
 */

/** Zend_Form_Decorator_Interface */

/**
 * Zend_Form_Decorator_Abstract.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */
#[AllowDynamicProperties]
abstract class Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Interface
{
    /**
     * Placement constants.
     */
    public const APPEND = 'APPEND';
    public const PREPEND = 'PREPEND';

    /**
     * Default placement: append.
     *
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * @var Zend_Form|Zend_Form_Element
     */
    protected $_element;

    /**
     * Decorator options.
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Separator between new content and old.
     *
     * @var string
     */
    protected $_separator = PHP_EOL;

    /**
     * Constructor.
     *
     * @param  array|Zend_Config $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set options.
     *
     * @return Zend_Form_Decorator_Abstract
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;

        return $this;
    }

    /**
     * Set options from config object.
     *
     * @return Zend_Form_Decorator_Abstract
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set option.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return Zend_Form_Decorator_Abstract
     */
    public function setOption($key, $value)
    {
        $this->_options[(string) $key] = $value;

        return $this;
    }

    /**
     * Get option.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        $key = (string) $key;
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return null;
    }

    /**
     * Retrieve options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Remove single option.
     *
     * @param mixed $key
     */
    public function removeOption($key)
    {
        if (null !== $this->getOption($key)) {
            unset($this->_options[$key]);

            return true;
        }

        return false;
    }

    /**
     * Clear all options.
     *
     * @return Zend_Form_Decorator_Abstract
     */
    public function clearOptions()
    {
        $this->_options = [];

        return $this;
    }

    /**
     * Set current form element.
     *
     * @param  Zend_Form|Zend_Form_Element $element
     *
     * @return Zend_Form_Decorator_Abstract
     */
    public function setElement($element)
    {
        if ((!$element instanceof Zend_Form_Element)
            && (!$element instanceof Zend_Form)
            && (!$element instanceof Zend_Form_DisplayGroup)) {
            throw new Zend_Form_Decorator_Exception('Invalid element type passed to decorator');
        }

        $this->_element = $element;

        return $this;
    }

    /**
     * Retrieve current element.
     *
     * @return Zend_Form|Zend_Form_Element
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Determine if decorator should append or prepend content.
     *
     * @return string
     */
    public function getPlacement()
    {
        $placement = $this->_placement;
        if (null !== ($placementOpt = $this->getOption('placement'))) {
            $placementOpt = strtoupper($placementOpt);
            switch ($placementOpt) {
                case self::APPEND:
                case self::PREPEND:
                    $placement = $this->_placement = $placementOpt;

                    break;
                case false:
                    $placement = $this->_placement = null;

                    break;
                default:
                    break;
            }
            $this->removeOption('placement');
        }

        return $placement;
    }

    /**
     * Retrieve separator to use between old and new content.
     *
     * @return string
     */
    public function getSeparator()
    {
        $separator = $this->_separator;
        if (null !== ($separatorOpt = $this->getOption('separator'))) {
            $separator = $this->_separator = (string) $separatorOpt;
            $this->removeOption('separator');
        }

        return $separator;
    }

    /**
     * Decorate content and/or element.
     *
     * @param  string $content
     *
     * @return string
     */
    public function render($content)
    {
        throw new Zend_Form_Decorator_Exception('render() not implemented');
    }
}
