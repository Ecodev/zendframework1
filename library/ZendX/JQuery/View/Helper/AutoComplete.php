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
require_once 'ZendX/JQuery/View/Helper/UiWidget.php';

/**
 * jQuery Autocomplete View Helper.
 *
 * @uses 	   Zend_Json, Zend_View_Helper_FormText
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_AutoComplete extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Builds an AutoComplete ready input field.
     *
     * This view helper builds an input field with the {@link Zend_View_Helper_FormText} FormText
     * Helper and adds additional javascript to the jQuery stack to initialize an AutoComplete
     * field. Make sure you have set one out of the two following options: $params['data'] or
     * $params['url']. The first one accepts an array as data input to the autoComplete, the
     * second accepts an url, where the autoComplete content is returned from. For the format
     * see jQuery documentation.
     *
     * @see   http://docs.jquery.com/UI/Autocomplete
     *
     * @param  string $id
     * @param  string $value
     *
     * @return string
     */
    public function autoComplete($id, $value = null, array $params = [], array $attribs = [])
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if (!isset($params['source'])) {
            if (isset($params['url'])) {
                $params['source'] = $params['url'];
                unset($params['url']);
            } elseif (isset($params['data'])) {
                $params['source'] = $params['data'];
                unset($params['data']);
            } else {
                require_once 'ZendX/JQuery/Exception.php';

                throw new ZendX_JQuery_Exception(
                    "Cannot construct AutoComplete field without specifying 'source' field, "
                    . 'either an url or an array of elements.'
                );
            }
        }

        $params = ZendX_JQuery::encodeJson($params);

        $js = sprintf('%s("#%s").autocomplete(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );

        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
    }
}
