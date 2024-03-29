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

/** Zend_View_Helper_FormElement */

/**
 * Helper for rendering fieldsets.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_View_Helper_Fieldset extends Zend_View_Helper_FormElement
{
    /**
     * Render HTML form.
     *
     * @param  string $name Form name
     * @param  string $content Form content
     * @param  array $attribs HTML form attributes
     *
     * @return string
     */
    public function fieldset($name, $content, $attribs = null)
    {
        $escape = null;
        $info = $this->_getInfo($name, $content, $attribs);
        extract($info);

        // get legend
        $legend = '';
        if (isset($attribs['legend'])) {
            $legendString = trim($attribs['legend']);
            if (!empty($legendString)) {
                $legend = '<legend>'
                        . (($escape) ? $this->view->escape($legendString) : $legendString)
                        . '</legend>' . PHP_EOL;
            }
            unset($attribs['legend']);
        }

        // get id
        if (!empty($id)) {
            $id = ' id="' . $this->view->escape($id) . '"';
        } else {
            $id = '';
        }

        // render fieldset
        $xhtml = '<fieldset'
               . $id
               . $this->_htmlAttribs($attribs)
               . '>'
               . $legend
               . $content
               . '</fieldset>';

        return $xhtml;
    }
}
