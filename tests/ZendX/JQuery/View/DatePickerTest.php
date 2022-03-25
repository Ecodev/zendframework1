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
require_once 'jQueryTestCase.php';

require_once 'Zend/Locale.php';
require_once 'ZendX/JQuery/View/Helper/DatePicker.php';

class ZendX_JQuery_View_DatePickerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->datePicker('element', '');

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->datePicker('elem1', '', ['option' => 'true']);

        $jquery = $this->view->jQuery()->__toString();
        $this->assertStringContainsString('datepicker(', $jquery);
        $this->assertStringContainsString('"option":"true"', $jquery);
    }

    public function testShouldCreateInputField()
    {
        $element = $this->view->datePicker('elem1', '01.01.2007');

        $this->assertEquals(['$("#elem1").datepicker({});'], $this->view->jQuery()->getOnLoadActions());
        $this->assertStringContainsString('<input', $element);
        $this->assertStringContainsString('id="elem1"', $element);
        $this->assertStringContainsString('value="01.01.2007"', $element);
    }

    public function testDatePickerSupportsLocaleDe()
    {
        $view = $this->getView();
        $locale = new Zend_Locale('de');
        Zend_Registry::set(\Zend_Locale::class, $locale);
        $view->datePicker('dp1');

        $this->assertEquals([
            '$("#dp1").datepicker({"dateFormat":"dd.mm.yy"});',
        ], $view->jQuery()->getOnLoadActions());
    }

    public function testDatePickerSupportsLocaleEn()
    {
        $view = $this->getView();

        $locale = new Zend_Locale('en');
        Zend_Registry::set(\Zend_Locale::class, $locale);
        $view->datePicker('dp2');

        $this->assertEquals([
            '$("#dp2").datepicker({"dateFormat":"M d, yy"});',
        ], $view->jQuery()->getOnLoadActions());
    }

    public function testDatePickerSupportsLocaleFr()
    {
        $view = $this->getView();

        $locale = new Zend_Locale('fr');
        Zend_Registry::set(\Zend_Locale::class, $locale);
        $view->datePicker('dp3');

        $this->assertEquals([
            '$("#dp3").datepicker({"dateFormat":"d M yy"});',
        ], $view->jQuery()->getOnLoadActions());
    }

    /**
     * @group ZF-5615
     */
    public function testDatePickerLocalization()
    {
        $dpFormat = ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat('MMM d, yyyy');
        $this->assertEquals('M d, yy', $dpFormat, "'MMM d, yyyy' has to be converted to 'M d, yy'.");
    }
}
