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
 * Base Form Element for jQuery View Helpers.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_Form_Element_UiWidget extends Zend_Form_Element
{
    /**
     * jQuery related parameters of this form element.
     *
     * @var array
     */
    public $jQueryParams = [];

    /**
     * Just here to prevent errors.
     *
     * @var array
     */
    public $options = [];

    /**
     * Constructor.
     *
     * @param mixed $spec
     * @param mixed $options
     */
    public function __construct($spec, $options = null)
    {
        $this->addPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator');
        parent::__construct($spec, $options);
    }

    /**
     * Get jQuery related parameter of this form element.
     *
     * @param  string $key
     *
     * @return string
     */
    public function getJQueryParam($key)
    {
        $key = (string) $key;

        return $this->jQueryParams[$key];
    }

    /**
     * Get all currently known jQuery related parameters of this element.
     *
     * @return array
     */
    public function getJQueryParams()
    {
        return $this->jQueryParams;
    }

    /**
     * Set a jQuery related parameter of this form element.
     *
     * @param  string $key
     * @param  string $value
     *
     * @return ZendX_JQuery_Form_Element_UiWidget
     */
    public function setJQueryParam($key, $value)
    {
        $key = (string) $key;
        $this->jQueryParams[$key] = $value;

        return $this;
    }

    /**
     * Set an array of jQuery related options for this element (merging with old options).
     *
     * @param  array $params
     *
     * @return ZendX_JQuery_Form_Element_UiWidget
     */
    public function setJQueryParams($params)
    {
        $this->jQueryParams = array_merge($this->jQueryParams, $params);

        return $this;
    }

    /**
     * Load default decorators.
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('UiWidgetElement')
                ->addDecorator('Errors')
                ->addDecorator('Description', ['tag' => 'p', 'class' => 'description'])
                ->addDecorator('HtmlTag', ['tag' => 'dd'])
                ->addDecorator('Label', ['tag' => 'dt']);
        }
    }

    /**
     * Set the view object.
     *
     * Ensures that the view object has the jQuery view helper path set.
     *
     * @return ZendX_JQuery_Form_Element_UiWidget
     */
    public function setView(?Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) {
                $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            }
        }

        return parent::setView($view);
    }

    /**
     * Retrieve all decorators.
     *
     * @return array
     */
    public function getDecorators()
    {
        $decorators = parent::getDecorators();
        if (count($decorators) > 0) {
            // Only check this if there are decorators present, otherwise it could
            // be that the decorators have not been initialized yet.
            $foundUiWidgetElementMarker = false;
            foreach ($decorators as $decorator) {
                if ($decorator instanceof ZendX_JQuery_Form_Decorator_UiWidgetElementMarker) {
                    $foundUiWidgetElementMarker = true;
                }
            }
            if ($foundUiWidgetElementMarker === false) {
                throw new ZendX_JQuery_Form_Exception(
                    'Cannot render jQuery form element without at least one decorator '
                    . "implementing the 'ZendX_JQuery_Form_Decorator_UiWidgetElementMarker' interface. "
                    . "Default decorator for this marker interface is the 'ZendX_JQuery_Form_Decorator_UiWidgetElement'. "
                    . 'Hint: The ViewHelper decorator does not render jQuery elements correctly.'
                );
            }
        }

        return $decorators;
    }
}
