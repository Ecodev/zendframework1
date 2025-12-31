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
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version     $Id$
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once 'UiWidget.php';

/**
 * jQuery Pane Base class, adds captureStart/captureEnd functionality for panes.
 *
 * @uses       ZendX_JQuery_View_Helper_JQuery_Container
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
abstract class ZendX_JQuery_View_Helper_UiWidgetPane extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Capture Lock information.
     *
     * @var array
     */
    protected $_captureLock = [];

    /**
     * Current capture additional information.
     *
     * @var array
     */
    protected $_captureInfo = [];

    /**
     * Begin capturing content for layout container.
     *
     * @param  string $id
     * @param  string $name
     *
     * @return bool
     */
    public function captureStart($id, $name, array $options = [])
    {
        if (array_key_exists($id, $this->_captureLock)) {
            throw new ZendX_JQuery_View_Exception(sprintf('Lock already exists for id "%s"', $id));
        }

        $this->_captureLock[$id] = true;
        $this->_captureInfo[$id] = [
            'name' => $name,
            'options' => $options,
        ];

        return ob_start();
    }

    /**
     * Finish capturing content for layout container.
     *
     * @param  string $id
     *
     * @return string
     */
    public function captureEnd($id)
    {
        $name = null;
        $options = null;
        if (!array_key_exists($id, $this->_captureLock)) {
            throw new ZendX_JQuery_View_Exception(sprintf('No capture lock exists for id "%s"; nothing to capture', $id));
        }

        $content = ob_get_clean();
        extract($this->_captureInfo[$id]);
        unset($this->_captureLock[$id], $this->_captureInfo[$id]);

        return $this->_addPane($id, $name, $content, $options);
    }

    /**
     * Add an additional pane to the current Widget Container.
     *
     * @param string $id
     * @param string $name
     * @param string $content
     */
    abstract protected function _addPane($id, $name, $content, array $options = []);
}
