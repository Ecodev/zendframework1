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

/** Zend_Form_Decorator_Abstract */

/**
 * Zend_Form_Decorator_Callback.
 *
 * Execute an arbitrary callback to decorate an element. Callbacks should take
 * three arguments, $content, $element, and $options:
 *
 * function mycallback($content, $element, array $options)
 * {
 * }
 *
 * and should return a string. ($options are whatever options were provided to
 * the decorator.)
 *
 * To specify a callback, pass a valid callback as the 'callback' option.
 *
 * Callback results will be either appended, prepended, or replace the provided
 * content. To replace the content, specify a placement of boolean false;
 * defaults to append content.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_Callback extends Zend_Form_Decorator_Abstract
{
    /**
     * Callback.
     *
     * @var array|string
     */
    protected $_callback;

    /**
     * Set callback.
     *
     * @param  callable $callback
     *
     * @return Zend_Form_Decorator_Callback
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Zend_Form_Exception('Invalid callback provided to callback decorator');
        }
        $this->_callback = $callback;

        return $this;
    }

    /**
     * Get registered callback.
     *
     * If not previously registered, checks to see if it exists in registered
     * options.
     *
     * @return null|array|string
     */
    public function getCallback()
    {
        if (null === $this->_callback) {
            if (null !== ($callback = $this->getOption('callback'))) {
                $this->setCallback($callback);
                $this->removeOption('callback');
            }
        }

        return $this->_callback;
    }

    /**
     * Render.
     *
     * If no callback registered, returns callback. Otherwise, gets return
     * value of callback and either appends, prepends, or replaces passed in
     * content.
     *
     * @param  string $content
     *
     * @return string
     */
    public function render($content)
    {
        $callback = $this->getCallback();
        if (null === $callback) {
            return $content;
        }

        $placement = $this->getPlacement();
        $separator = $this->getSeparator();

        $response = call_user_func($callback, $content, $this->getElement(), $this->getOptions());

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $response;
            case self::PREPEND:
                return $response . $separator . $content;
            default:
                // replace content
                return $response;
        }
    }
}
