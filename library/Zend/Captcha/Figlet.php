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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @see Zend_Captcha_Word */

/** @see Zend_Text_Figlet */

/**
 * Captcha based on figlet text rendering service.
 *
 * Note that this engine seems not to like numbers
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */
#[AllowDynamicProperties]
class Zend_Captcha_Figlet extends Zend_Captcha_Word
{
    /**
     * Figlet text renderer.
     *
     * @var Zend_Text_Figlet
     */
    protected $_figlet;

    /**
     * Constructor.
     *
     * @param null|array|string|Zend_Config $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->_figlet = new Zend_Text_Figlet($options);
    }

    /**
     * Generate new captcha.
     *
     * @return string
     */
    public function generate()
    {
        $this->_useNumbers = false;

        return parent::generate();
    }

    /**
     * Display the captcha.
     *
     * @param mixed $element
     *
     * @return string
     */
    public function render(?Zend_View_Interface $view = null, $element = null)
    {
        return '<pre>'
             . $this->_figlet->render($this->getWord())
             . "</pre>\n";
    }
}
