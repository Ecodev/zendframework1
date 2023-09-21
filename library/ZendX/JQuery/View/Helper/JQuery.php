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
 * jQuery Helper. Functions as a stack for code and loads all jQuery dependencies.
 *
 * @uses 	   Zend_Json
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_JQuery extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * jQuery no Conflict Mode.
     *
     * @see	      http://docs.jquery.com/Using_jQuery_with_Other_Libraries
     *
     * @staticvar Boolean Status of noConflict Mode
     */
    private static bool $noConflictMode = false;

    /**
     * Initialize helper.
     *
     * Retrieve container from registry or create new container and store in
     * registry.
     */
    public function __construct()
    {
        $registry = Zend_Registry::getInstance();
        if (!isset($registry[self::class])) {
            $container = new ZendX_JQuery_View_Helper_JQuery_Container();
            $registry[self::class] = $container;
        }
        $this->_container = $registry[self::class];
    }

    /**
     * Return jQuery View Helper class, to execute jQuery library related functions.
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function jQuery()
    {
        return $this->_container;
    }

    /**
     * Set view object.
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        $this->_container->setView($view);
    }

    /**
     * Proxy to container methods.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->_container, $method)) {
            throw new Zend_View_Exception(sprintf('Invalid method "%s" called on jQuery view helper', $method));
        }

        return call_user_func_array([$this->_container, $method], $args);
    }

    /**
     * Enable the jQuery internal noConflict Mode to work with
     * other Javascript libraries. Will setup jQuery in the variable
     * $j instead of $ to overcome conflicts.
     *
     * @see http://docs.jquery.com/Using_jQuery_with_Other_Libraries
     */
    public static function enableNoConflictMode()
    {
        self::$noConflictMode = true;
    }

    /**
     * Disable noConflict Mode of jQuery if this was previously enabled.
     */
    public static function disableNoConflictMode()
    {
        self::$noConflictMode = false;
    }

    /**
     * Return current status of the jQuery no Conflict Mode.
     *
     * @return bool
     */
    public static function getNoConflictMode()
    {
        return self::$noConflictMode;
    }

    /**
     * Return current jQuery handler based on noConflict mode settings.
     *
     * @return string
     */
    public static function getJQueryHandler()
    {
        return (self::getNoConflictMode() == true) ? '$j' : '$';
    }
}
