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

/** Zend_Form_Element_Submit */

/**
 * Button form element.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */
#[AllowDynamicProperties]
class Zend_Form_Element_Button extends Zend_Form_Element_Submit
{
    /**
     * Use formButton view helper by default.
     *
     * @var string
     */
    public $helper = 'formButton';

    /**
     * Validate element value (pseudo).
     *
     * There is no need to reset the value
     *
     * @param  mixed $value Is always ignored
     * @param  mixed $context Is always ignored
     *
     * @return bool Returns always TRUE
     */
    public function isValid($value, $context = null)
    {
        return true;
    }
}
