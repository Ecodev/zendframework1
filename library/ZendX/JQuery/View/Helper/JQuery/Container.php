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
 * jQuery View Helper. Transports all jQuery stack and render information across all views.
 *
 * @uses       ZendX_JQuery_View_Helper_JQuery_Container
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_JQuery_Container
{
    /**
     * Path to local webserver jQuery library.
     *
     * @var string
     */
    protected $_jqueryLibraryPath;

    /**
     * Additional javascript files that for jQuery Helper components.
     *
     * @var array
     */
    protected $_javascriptSources = [];

    /**
     * Indicates wheater the jQuery View Helper is enabled.
     *
     * @var bool
     */
    protected $_enabled = false;

    /**
     * Indicates if a capture start method for javascript or onLoad has been called.
     *
     * @var bool
     */
    protected $_captureLock = false;

    /**
     * Additional javascript statements that need to be executed after jQuery lib.
     *
     * @var array
     */
    protected $_javascriptStatements = [];

    /**
     * Additional stylesheet files for jQuery related components.
     *
     * @var array
     */
    protected $_stylesheets = [];

    /**
     * jQuery onLoad statements Stack.
     *
     * @var array
     */
    protected $_onLoadActions = [];

    /**
     * View is rendered in XHTML or not.
     *
     * @var bool
     */
    protected $_isXhtml = false;

    /**
     * Default CDN jQuery Library version.
     *
     * @var string
     */
    protected $_version = ZendX_JQuery::DEFAULT_JQUERY_VERSION;

    /**
     * Default Render Mode (all parts).
     *
     * @var int
     */
    protected $_renderMode = ZendX_JQuery::RENDER_ALL;

    /**
     * jQuery UI Library Enabled.
     *
     * @var bool
     */
    protected $_uiEnabled = false;

    /**
     * Local jQuery UI Path. Use Google CDN if
     * variable is null.
     *
     * @var string
     */
    protected $_uiPath;

    /**
     * jQuery UI Google CDN Version.
     *
     * @var string
     */
    protected $_uiVersion = ZendX_JQuery::DEFAULT_UI_VERSION;

    /**
     * Load CDN Path from SSL or Non-SSL?
     *
     * @var bool
     */
    protected $_loadSslCdnPath = false;

    /**
     * View Instance.
     *
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * Set view object.
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * Enable jQuery.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function enable()
    {
        $this->_enabled = true;

        return $this;
    }

    /**
     * Disable jQuery.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function disable()
    {
        $this->uiDisable();
        $this->_enabled = false;

        return $this;
    }

    /**
     * Is jQuery enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set the version of the jQuery library used.
     *
     * @param string $version
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setVersion($version)
    {
        $this->_version = $version;

        return $this;
    }

    /**
     * Get the version used with the jQuery library.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Use CDN, using version specified. Currently supported
     * by Googles Ajax Library API are: 1.2.3, 1.2.6.
     *
     * @deprecated As of version 1.8, use {@link setVersion()} instead.
     *
     * @param  string $version
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setCdnVersion($version = null)
    {
        return $this->setVersion($version);
    }

    /**
     * Get CDN version.
     *
     * @deprecated As of version 1.8, use {@link getVersion()} instead.
     *
     * @return string
     */
    public function getCdnVersion()
    {
        return $this->getVersion();
    }

    /**
     * Set Use SSL on CDN Flag.
     *
     * @param bool $flag
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setCdnSsl($flag)
    {
        $this->_loadSslCdnPath = (boolean) $flag;

        return $this;
    }

    /**
     * Get Flag of SSL on CDN.
     *
     * @return bool True if SSL is used on CDN
     */
    public function getCdnSsl()
    {
        return $this->_loadSslCdnPath;
    }

    /**
     * Are we using the CDN?
     *
     * @return bool
     */
    public function useCdn()
    {
        return !$this->useLocalPath();
    }

    /**
     * Set path to local jQuery library.
     *
     * @param  string $path
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setLocalPath($path)
    {
        $this->_jqueryLibraryPath = (string) $path;

        return $this;
    }

    /**
     * Enable jQuery UI Library Rendering.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function uiEnable()
    {
        $this->enable();
        $this->_uiEnabled = true;

        return $this;
    }

    /**
     * Disable jQuery UI Library Rendering.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function uiDisable()
    {
        $this->_uiEnabled = false;

        return $this;
    }

    /**
     * Check wheater currently the jQuery UI library is enabled.
     *
     * @return bool
     */
    public function uiIsEnabled()
    {
        return $this->_uiEnabled;
    }

    /**
     * Set jQuery UI version used.
     *
     * @param  string $version
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setUiVersion($version)
    {
        $this->_uiVersion = $version;

        return $this;
    }

    /**
     * Get jQuery UI Version used.
     *
     * @return string
     */
    public function getUiVersion()
    {
        return $this->_uiVersion;
    }

    /**
     * Set jQuery UI CDN Version.
     *
     * @deprecated As of 1.8 use {@link setUiVersion()}
     *
     * @param string $version
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setUiCdnVersion($version = '1.5.2')
    {
        return $this->setUiVersion($version);
    }

    /**
     * Return jQuery UI CDN Version.
     *
     * @deprecated As of 1.8 use {@link getUiVersion()}
     *
     * @return string
     */
    public function getUiCdnVersion()
    {
        return $this->getUiVersion();
    }

    /**
     * Set local path to jQuery UI library.
     *
     * @param string $path
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setUiLocalPath($path)
    {
        $this->_uiPath = (string) $path;

        return $this;
    }

    /**
     * Return the local jQuery UI Path if set.
     *
     * @return string
     */
    public function getUiPath()
    {
        return $this->_uiPath;
    }

    /**
     * Proxies to getUiPath() for consistency in function naming.
     *
     * @return string
     */
    public function getUiLocalPath()
    {
        return $this->getUiPath();
    }

    /**
     * Is the jQuery Ui loaded from local scope?
     *
     * @return bool
     */
    public function useUiLocal()
    {
        return (null === $this->_uiPath) ? false : true;
    }

    /**
     * Is the jQuery Ui enabled and loaded from CDN?
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function useUiCdn()
    {
        return !$this->useUiLocal();
    }

    /**
     * Get local path to jQuery.
     *
     * @return string
     */
    public function getLocalPath()
    {
        return $this->_jqueryLibraryPath;
    }

    /**
     * Are we using a local path?
     *
     * @return bool
     */
    public function useLocalPath()
    {
        return (null === $this->_jqueryLibraryPath) ? false : true;
    }

    /**
     * Start capturing routines to run onLoad.
     *
     * @return bool
     */
    public function onLoadCaptureStart()
    {
        if ($this->_captureLock) {
            throw new Zend_Exception('Cannot nest onLoad captures');
        }

        $this->_captureLock = true;

        return ob_start();
    }

    /**
     * Stop capturing routines to run onLoad.
     *
     * @return bool
     */
    public function onLoadCaptureEnd()
    {
        $data = ob_get_clean();
        $this->_captureLock = false;

        $this->addOnLoad($data);

        return true;
    }

    /**
     * Capture arbitrary javascript to include in jQuery script.
     *
     * @return bool
     */
    public function javascriptCaptureStart()
    {
        if ($this->_captureLock) {
            throw new Zend_Exception('Cannot nest captures');
        }

        $this->_captureLock = true;

        return ob_start();
    }

    /**
     * Finish capturing arbitrary javascript to include in jQuery script.
     *
     * @return bool
     */
    public function javascriptCaptureEnd()
    {
        $data = ob_get_clean();
        $this->_captureLock = false;

        $this->addJavascript($data);

        return true;
    }

    /**
     * Add a Javascript File to the include stack.
     *
     * @param string $path
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function addJavascriptFile($path)
    {
        $path = (string) $path;
        if (!in_array($path, $this->_javascriptSources)) {
            $this->_javascriptSources[] = $path;
        }

        return $this;
    }

    /**
     * Return all currently registered Javascript files.
     *
     * This does not include the jQuery library, which is handled by another retrieval
     * strategy.
     *
     * @return array
     */
    public function getJavascriptFiles()
    {
        return $this->_javascriptSources;
    }

    /**
     * Clear all currently registered Javascript files.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function clearJavascriptFiles()
    {
        $this->_javascriptSources = [];

        return $this;
    }

    /**
     * Add arbitrary javascript to execute in jQuery JS container.
     *
     * @param  string $js
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function addJavascript($js)
    {
        $this->_javascriptStatements[] = $js;
        $this->enable();

        return $this;
    }

    /**
     * Return all registered javascript statements.
     *
     * @return array
     */
    public function getJavascript()
    {
        return $this->_javascriptStatements;
    }

    /**
     * Clear arbitrary javascript stack.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function clearJavascript()
    {
        $this->_javascriptStatements = [];

        return $this;
    }

    /**
     * Add a stylesheet.
     *
     * @param  string $path
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function addStylesheet($path)
    {
        $path = (string) $path;
        if (!in_array($path, $this->_stylesheets)) {
            $this->_stylesheets[] = (string) $path;
        }

        return $this;
    }

    /**
     * Retrieve registered stylesheets.
     *
     * @return array
     */
    public function getStylesheets()
    {
        return $this->_stylesheets;
    }

    /**
     * Clear all currently registered stylesheets files.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function clearStylesheets()
    {
        $this->_stylesheets = [];

        return $this;
    }

    /**
     * Add a script to execute onLoad.
     *
     * @param  string $callback Lambda
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function addOnLoad($callback)
    {
        if (!in_array($callback, $this->_onLoadActions, true)) {
            $this->_onLoadActions[] = $callback;
        }
        $this->enable();

        return $this;
    }

    /**
     * Retrieve all registered onLoad actions.
     *
     * @return array
     */
    public function getOnLoadActions()
    {
        return $this->_onLoadActions;
    }

    /**
     * Clear the onLoadActions stack.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function clearOnLoadActions()
    {
        $this->_onLoadActions = [];

        return $this;
    }

    /**
     * Set which parts of the jQuery enviroment should be rendered.
     *
     * This function allows for a gradual refactoring of the jQuery code
     * rendered by calling __toString(). Use ZendX_JQuery::RENDER_*
     * constants. By default all parts of the enviroment are rendered.
     *
     * @see    ZendX_JQuery::RENDER_ALL
     *
     * @param  int $mask
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function setRenderMode($mask)
    {
        $this->_renderMode = $mask;

        return $this;
    }

    /**
     * Return bitmask of the current Render Mode.
     *
     * @return int
     */
    public function getRenderMode()
    {
        return $this->_renderMode;
    }

    /**
     * String representation of jQuery environment.
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $this->_isXhtml = $this->view->doctype()->isXhtml();

        $html = $this->_renderStylesheets() . PHP_EOL
               . $this->_renderScriptTags() . PHP_EOL
               . $this->_renderExtras();

        return $html;
    }

    /**
     * Render jQuery stylesheets.
     *
     * @return string
     */
    protected function _renderStylesheets()
    {
        if (0 == ($this->getRenderMode() & ZendX_JQuery::RENDER_STYLESHEETS)) {
            return '';
        }

        foreach ($this->getStylesheets() as $stylesheet) {
            $stylesheets[] = $stylesheet;
        }

        if (empty($stylesheets)) {
            return '';
        }

        array_reverse($stylesheets);
        $style = '';
        foreach ($stylesheets as $stylesheet) {
            if ($this->view instanceof Zend_View_Abstract) {
                $closingBracket = ($this->view->doctype()->isXhtml()) ? ' />' : '>';
            } else {
                $closingBracket = ' />';
            }

            $style .= '<link rel="stylesheet" href="' . $stylesheet . '" '
                      . 'type="text/css" media="screen"' . $closingBracket . PHP_EOL;
        }

        return $style;
    }

    /**
     * Renders all javascript file related stuff of the jQuery enviroment.
     *
     * @return string
     */
    protected function _renderScriptTags()
    {
        $scriptTags = '';
        if (($this->getRenderMode() & ZendX_JQuery::RENDER_LIBRARY) > 0) {
            $source = $this->_getJQueryLibraryPath();

            $scriptTags .= '<script type="text/javascript" src="' . $source . '"></script>' . PHP_EOL;

            if ($this->uiIsEnabled()) {
                $uiPath = $this->_getJQueryUiLibraryPath();
                $scriptTags .= '<script type="text/javascript" src="' . $uiPath . '"></script>' . PHP_EOL;
            }

            if (ZendX_JQuery_View_Helper_JQuery::getNoConflictMode() == true) {
                $scriptTags .= '<script type="text/javascript">var $j = jQuery.noConflict();</script>' . PHP_EOL;
            }
        }

        if (($this->getRenderMode() & ZendX_JQuery::RENDER_SOURCES) > 0) {
            foreach ($this->getJavascriptFiles() as $javascriptFile) {
                $scriptTags .= '<script type="text/javascript" src="' . $javascriptFile . '"></script>' . PHP_EOL;
            }
        }

        return $scriptTags;
    }

    /**
     * Renders all javascript code related stuff of the jQuery enviroment.
     *
     * @return string
     */
    protected function _renderExtras()
    {
        $onLoadActions = [];
        if (($this->getRenderMode() & ZendX_JQuery::RENDER_JQUERY_ON_LOAD) > 0) {
            foreach ($this->getOnLoadActions() as $callback) {
                $onLoadActions[] = $callback;
            }
        }

        $javascript = '';
        if (($this->getRenderMode() & ZendX_JQuery::RENDER_JAVASCRIPT) > 0) {
            $javascript = implode("\n    ", $this->getJavascript());
        }

        $content = '';

        if (!empty($onLoadActions)) {
            if (true === ZendX_JQuery_View_Helper_JQuery::getNoConflictMode()) {
                $content .= '$j(document).ready(function() {' . "\n    ";
            } else {
                $content .= '$(document).ready(function() {' . "\n    ";
            }
            $content .= implode("\n    ", $onLoadActions) . "\n";
            $content .= '});' . "\n";
        }

        if (!empty($javascript)) {
            $content .= $javascript . "\n";
        }

        if (preg_match('/^\s*$/s', $content)) {
            return '';
        }

        $html = '<script type="text/javascript">' . PHP_EOL
              . (($this->_isXhtml) ? '//<![CDATA[' : '//<!--') . PHP_EOL
              . $content
              . (($this->_isXhtml) ? '//]]>' : '//-->') . PHP_EOL
              . PHP_EOL . '</script>';

        return $html;
    }

    /**
     * @return string
     */
    protected function _getJQueryLibraryBaseCdnUri()
    {
        if ($this->_loadSslCdnPath == true) {
            $baseUri = ZendX_JQuery::CDN_BASE_GOOGLE_SSL;
        } else {
            $baseUri = ZendX_JQuery::CDN_BASE_GOOGLE;
        }

        return $baseUri;
    }

    /**
     * @return string
     */
    protected function _getJQueryUiLibraryBaseCdnUri()
    {
        if ($this->_loadSslCdnPath == true) {
            $baseUri = ZendX_JQuery::CDN_BASE_GOOGLE_SSL;
        } else {
            $baseUri = ZendX_JQuery::CDN_BASE_GOOGLE;
        }

        return $baseUri;
    }

    /**
     * Internal function that constructs the include path of the jQuery library.
     *
     * @return string
     */
    protected function _getJQueryLibraryPath()
    {
        if ($this->_jqueryLibraryPath != null) {
            $source = $this->_jqueryLibraryPath;
        } else {
            $baseUri = $this->_getJQueryLibraryBaseCdnUri();
            $source = $baseUri
                     . ZendX_JQuery::CDN_SUBFOLDER_JQUERY
                     . $this->getVersion()
                     . ZendX_JQuery::CDN_JQUERY_PATH_GOOGLE;
        }

        return $source;
    }

    /**
     * Internal function that constructs the include path of the jQueryUI library.
     *
     * @return string
     */
    protected function _getJQueryUiLibraryPath()
    {
        if ($this->useUiCdn()) {
            $baseUri = $this->_getJQueryLibraryBaseCdnUri();
            $uiPath = $baseUri
                     . ZendX_JQuery::CDN_SUBFOLDER_JQUERYUI
                     . $this->getUiVersion()
                     . '/jquery-ui.min.js';
        } elseif ($this->useUiLocal()) {
            $uiPath = $this->getUiPath();
        }

        return $uiPath;
    }
}
