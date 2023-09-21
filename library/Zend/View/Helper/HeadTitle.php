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
 * @version    $Id$
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving title element for HTML head.
 *
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_View_Helper_HeadTitle extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * Registry key for placeholder.
     *
     * @var string
     */
    protected $_regKey = \Zend_View_Helper_HeadTitle::class;

    /**
     * Whether or not auto-translation is enabled.
     *
     * @var bool
     */
    protected $_translate = false;

    /**
     * Translation object.
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translator;

    /**
     * Default title rendering order (i.e. order in which each title attached).
     *
     * @var string
     */
    protected $_defaultAttachOrder;

    /**
     * Retrieve placeholder for title element and optionally set state.
     *
     * @param  string $title
     * @param  string $setType
     *
     * @return Zend_View_Helper_HeadTitle
     */
    public function headTitle($title = null, $setType = null)
    {
        if (null === $setType) {
            $setType = $this->getDefaultAttachOrder() ?? Zend_View_Helper_Placeholder_Container_Abstract::APPEND;
        }
        $title = (string) $title;
        if ($title !== '') {
            if ($setType == Zend_View_Helper_Placeholder_Container_Abstract::SET) {
                $this->set($title);
            } elseif ($setType == Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) {
                $this->prepend($title);
            } else {
                $this->append($title);
            }
        }

        return $this;
    }

    /**
     * Set a default order to add titles.
     *
     * @param string $setType
     */
    public function setDefaultAttachOrder($setType)
    {
        if (!in_array($setType, [
            Zend_View_Helper_Placeholder_Container_Abstract::APPEND,
            Zend_View_Helper_Placeholder_Container_Abstract::SET,
            Zend_View_Helper_Placeholder_Container_Abstract::PREPEND,
        ])) {
            throw new Zend_View_Exception("You must use a valid attach order: 'PREPEND', 'APPEND' or 'SET'");
        }

        $this->_defaultAttachOrder = $setType;

        return $this;
    }

    /**
     * Get the default attach order, if any.
     *
     * @return mixed
     */
    public function getDefaultAttachOrder()
    {
        return $this->_defaultAttachOrder;
    }

    /**
     * Sets a translation Adapter for translation.
     *
     * @param  Zend_Translate|Zend_Translate_Adapter $translate
     *
     * @return Zend_View_Helper_HeadTitle
     */
    public function setTranslator($translate)
    {
        if ($translate instanceof Zend_Translate_Adapter) {
            $this->_translator = $translate;
        } elseif ($translate instanceof Zend_Translate) {
            $this->_translator = $translate->getAdapter();
        } else {
            $e = new Zend_View_Exception('You must set an instance of Zend_Translate or Zend_Translate_Adapter');
            $e->setView($this->view);

            throw $e;
        }

        return $this;
    }

    /**
     * Retrieve translation object.
     *
     * If none is currently registered, attempts to pull it from the registry
     * using the key 'Zend_Translate'.
     *
     * @return null|Zend_Translate_Adapter
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered(\Zend_Translate::class)) {
                $this->setTranslator(Zend_Registry::get(\Zend_Translate::class));
            }
        }

        return $this->_translator;
    }

    /**
     * Enables translation.
     *
     * @return Zend_View_Helper_HeadTitle
     */
    public function enableTranslation()
    {
        $this->_translate = true;

        return $this;
    }

    /**
     * Disables translation.
     *
     * @return Zend_View_Helper_HeadTitle
     */
    public function disableTranslation()
    {
        $this->_translate = false;

        return $this;
    }

    /**
     * Turn helper into string.
     *
     * @param  null|string $indent
     * @param  null|string $locale
     *
     * @return string
     */
    public function toString($indent = null, $locale = null)
    {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = [];

        if ($this->_translate && $translator = $this->getTranslator()) {
            foreach ($this as $item) {
                $items[] = $translator->translate($item, $locale);
            }
        } else {
            foreach ($this as $item) {
                $items[] = $item;
            }
        }

        $separator = $this->getSeparator();
        $output = '';
        if (($prefix = $this->getPrefix())) {
            $output .= $prefix;
        }
        $output .= implode($separator, $items);
        if (($postfix = $this->getPostfix())) {
            $output .= $postfix;
        }

        $output = ($this->_autoEscape) ? $this->_escape($output) : $output;

        return $indent . '<title>' . $output . '</title>';
    }
}
