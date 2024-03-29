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
 * jQuery Dialog View Helper.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_DialogContainer extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create a jQuery UI Dialog filled with the given content.
     *
     * @see   http://docs.jquery.com/UI/Dialog
     *
     * @param  string $id
     * @param  string $content
     * @param  array $params
     * @param  array $attribs
     *
     * @return string
     */
    public function dialogContainer($id, $content, $params = [], $attribs = [])
    {
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }

        if (count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").dialog(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );
        $this->jquery->addOnLoad($js);

        $html = '<div'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $content
                . '</div>';

        return $html;
    }
}
