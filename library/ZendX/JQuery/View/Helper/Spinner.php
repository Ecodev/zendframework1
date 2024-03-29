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
 * jQuery Spinner View Helper.
 *
 * @uses 	   Zend_Json
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_Spinner extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create FormText field for numeric values that can be spinned through its values.
     *
     * @see   http://docs.jquery.com/UI/Spinner
     *
     * @param  string $id
     * @param  string $value
     *
     * @return string
     */
    public function spinner($id, $value = '', array $params = [], array $attribs = [])
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if (!isset($params['start']) && is_numeric($value)) {
            $params['start'] = $value;
        }

        if (count($params)) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").spinner(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );

        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
    }
}
