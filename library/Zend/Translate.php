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
class Zend_Translate
{
    /**
     * Adapter names constants.
     */
    public const AN_ARRAY = 'Array';

    public const LOCALE_DIRECTORY = 'directory';
    public const LOCALE_FILENAME = 'filename';

    /**
     * Adapter.
     */
    private \Zend_Translate_Adapter $_adapter;

    /**
     * Generates the standard translation object.
     *
     * @param  array|Zend_Config|Zend_Translate_Adapter $options Options to use
     * @param  array|string [$content] Path to content, or content itself
     * @param  string|Zend_Locale [$locale]
     */
    public function __construct($options = [])
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (func_num_args() > 1) {
            $args = func_get_args();
            $options = [];
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } elseif (!is_array($options)) {
            $options = ['adapter' => $options];
        }

        $this->setAdapter($options);
    }

    /**
     * Sets a new adapter.
     *
     * @param  array|Zend_Config|Zend_Translate_Adapter $options Options to use
     * @param  array|string [$content] Path to content, or content itself
     * @param  string|Zend_Locale [$locale]
     */
    public function setAdapter($options = [])
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (func_num_args() > 1) {
            $args = func_get_args();
            $options = [];
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } elseif (!is_array($options)) {
            $options = ['adapter' => $options];
        }

        if (Zend_Loader::isReadable('Zend/Translate/Adapter/' . ucfirst($options['adapter']) . '.php')) {
            $options['adapter'] = 'Zend_Translate_Adapter_' . ucfirst($options['adapter']);
        }

        if (!class_exists($options['adapter'])) {
            Zend_Loader::loadClass($options['adapter']);
        }

        $adapter = $options['adapter'];
        unset($options['adapter']);
        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof Zend_Translate_Adapter) {
            throw new Zend_Translate_Exception('Adapter ' . $adapter . ' does not extend Zend_Translate_Adapter');
        }
    }

    /**
     * Returns the adapters name and it's options.
     *
     * @return Zend_Translate_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Calls all methods from the adapter.
     *
     * @param mixed $method
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array([$this->_adapter, $method], $options);
        }

        throw new Zend_Translate_Exception("Unknown method '" . $method . "' called!");
    }
}
