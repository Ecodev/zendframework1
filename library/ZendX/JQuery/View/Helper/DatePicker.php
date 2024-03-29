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
 * jQuery Date Picker View Helper.
 *
 * @uses 	   Zend_View_Helper_FormText
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
class ZendX_JQuery_View_Helper_DatePicker extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create a jQuery UI Widget Date Picker.
     *
     * @see   http://docs.jquery.com/UI/Datepicker
     *
     * @param  string $id
     * @param  string $value
     * @param  array  $params jQuery Widget Parameters
     * @param  array  $attribs HTML Element Attributes
     *
     * @return string
     */
    public function datePicker($id, $value = null, array $params = [], array $attribs = [])
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if (!isset($params['dateFormat']) && Zend_Registry::isRegistered(\Zend_Locale::class)) {
            $params['dateFormat'] = self::resolveZendLocaleToDatePickerFormat();
        }

        // TODO: Allow translation of DatePicker Text Values to get this action from client to server
        $params = ZendX_JQuery::encodeJson($params);

        $js = sprintf('%s("#%s").datepicker(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );

        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
    }

    /**
     * A Check for Zend_Locale existance has already been done in {@link datePicker()}
     * this function only resolves the default format from Zend Locale to
     * a jQuery Date Picker readable format. This function can be potentially buggy
     * because of its easy nature and is therefore stripped from the core functionality
     * to be easily overriden.
     *
     * @param  string $format
     *
     * @return string
     */
    public static function resolveZendLocaleToDatePickerFormat($format = null)
    {
        if ($format == null) {
            $locale = Zend_Registry::get(\Zend_Locale::class);
            if (!($locale instanceof Zend_Locale)) {
                throw new ZendX_JQuery_Exception('Cannot resolve Zend Locale format by default, no application wide locale is set.');
            }
            /**
             * @see Zend_Locale_Format
             */
            $format = Zend_Locale_Format::getDateFormat($locale);
        }

        $dateFormat = [
            'EEEEE' => 'D', 'EEEE' => 'DD', 'EEE' => 'D', 'EE' => 'D', 'E' => 'D',
            'MMMM' => 'MM', 'MMM' => 'M', 'MM' => 'mm', 'M' => 'm',
            'YYYYY' => 'yy', 'YYYY' => 'yy', 'YYY' => 'yy', 'YY' => 'y', 'Y' => 'yy',
            'yyyyy' => 'yy', 'yyyy' => 'yy', 'yyy' => 'yy', 'yy' => 'y', 'y' => 'yy',
            'G' => '', 'e' => '', 'a' => '', 'h' => '', 'H' => '', 'm' => '',
            's' => '', 'S' => '', 'z' => '', 'Z' => '', 'A' => '',
        ];

        $newFormat = '';
        $isText = false;
        $i = 0;
        while ($i < strlen($format)) {
            $chr = $format[$i];
            if ($chr == '"' || $chr == "'") {
                $isText = !$isText;
            }
            $replaced = false;
            if ($isText == false) {
                foreach ($dateFormat as $zl => $jql) {
                    if (substr($format, $i, strlen($zl)) == $zl) {
                        $chr = $jql;
                        $i += strlen($zl);
                        $replaced = true;
                    }
                }
            }
            if ($replaced == false) {
                ++$i;
            }
            $newFormat .= $chr;
        }

        return $newFormat;
    }
}
