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
 * @see ZendX_JQuery_Form_Decorator_UiWidgetContainer
 */
require_once 'UiWidgetContainer.php';

/**
 * Form Decorator for jQuery Dialog View Helper.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_Form_Decorator_DialogContainer extends ZendX_JQuery_Form_Decorator_UiWidgetContainer
{
    protected $_helper = 'dialogContainer';

    /**
     * Render an jQuery UI Widget element using its associated view helper.
     *
     * Determine view helper from 'helper' option, or, if none set, from
     * the element type. Then call as
     * helper($element->getName(), $element->getValue(), $element->getAttribs())
     *
     * @param  string $content
     *
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            return $content;
        }

        $jQueryParams = $this->getJQueryParams();
        $attribs = $this->getOptions();

        $helper = $this->getHelper();
        $id = $element->getId() . '-container';

        return $view->$helper($id, $content, $jQueryParams, $attribs);
    }
}
