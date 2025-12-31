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

/** Zend_Registry */

/** Zend_View_Helper_Abstract.php */

/**
 * Helper for setting and retrieving the doctype.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class Zend_View_Helper_Doctype extends Zend_View_Helper_Abstract
{
    /**#@+
     * DocType constants
     */
    public const HTML5 = 'HTML5';

    /**
     * Default DocType.
     *
     * @var string
     */
    protected $_defaultDoctype = self::HTML5;

    /**
     * Registry containing current doctype and mappings.
     *
     * @var ArrayObject
     */
    protected $_registry;

    /**
     * Registry key in which helper is stored.
     *
     * @var string
     */
    protected $_regKey = Zend_View_Helper_Doctype::class;

    /**
     * Constructor.
     *
     * Map constants to doctype strings, and set default doctype
     */
    public function __construct()
    {
        if (!Zend_Registry::isRegistered($this->_regKey)) {
            $this->_registry = new ArrayObject([
                'doctypes' => [
                    self::HTML5 => '<!DOCTYPE html>',
                ],
            ]);
            Zend_Registry::set($this->_regKey, $this->_registry);
            $this->setDoctype($this->_defaultDoctype);
        } else {
            $this->_registry = Zend_Registry::get($this->_regKey);
        }
    }

    /**
     * Set or retrieve doctype.
     *
     * @param string $doctype
     *
     * @return Zend_View_Helper_Doctype
     */
    public function doctype($doctype = null)
    {
        if (null !== $doctype) {
            switch ($doctype) {
                case self::HTML5:
                    $this->setDoctype($doctype);

                    break;
            }
        }

        return $this;
    }

    /**
     * Set doctype.
     *
     * @param string $doctype
     *
     * @return Zend_View_Helper_Doctype
     */
    public function setDoctype($doctype)
    {
        $this->_registry['doctype'] = $doctype;

        return $this;
    }

    /**
     * Retrieve doctype.
     *
     * @return string
     */
    public function getDoctype()
    {
        return $this->_registry['doctype'];
    }

    /**
     * Get doctype => string mappings.
     *
     * @return array
     */
    public function getDoctypes()
    {
        return $this->_registry['doctypes'];
    }

    /**
     * Is doctype XHTML?
     *
     * @return bool
     */
    public function isXhtml()
    {
        return stristr($this->getDoctype(), 'xhtml') ? true : false;
    }

    /**
     * Is doctype strict?
     *
     * @return bool
     */
    public function isStrict()
    {
        return false;
    }

    /**
     * Is doctype HTML5? (HeadMeta uses this for validation).
     *
     * @return booleean
     */
    public function isHtml5()
    {
        return stristr($this->doctype(), '<!DOCTYPE html>') ? true : false;
    }

    /**
     * Is doctype RDFa?
     *
     * @return booleean
     */
    public function isRdfa()
    {
        return stristr($this->getDoctype(), 'rdfa') ? true : false;
    }

    /**
     * String representation of doctype.
     *
     * @return string
     */
    public function __toString()
    {
        $doctypes = $this->getDoctypes();

        return $doctypes[$this->getDoctype()];
    }
}
