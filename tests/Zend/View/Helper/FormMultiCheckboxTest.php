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
 *
 * @version    $Id$
 */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_FormMultiCheckbox.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_FormMultiCheckboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_FormMultiCheckboxTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        if (Zend_Registry::isRegistered(\Zend_View_Helper_Doctype::class)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[\Zend_View_Helper_Doctype::class]);
        }
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_FormMultiCheckbox();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        ob_end_clean();
    }

    public function testMultiCheckboxHelperRendersLabelledCheckboxesForEachOption()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formMultiCheckbox([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
        ]);
        foreach ($options as $key => $value) {
            $pattern = '#((<label[^>]*>.*?)(<input[^>]*?("' . $key . '").*?>)(.*?</label>))#';
            if (!preg_match($pattern, $html, $matches)) {
                $this->fail('Failed to match ' . $pattern . ': ' . $html);
            }
            $this->assertStringContainsString($value, $matches[5], var_export($matches, 1));
            $this->assertStringContainsString('type="checkbox"', $matches[3], var_export($matches, 1));
            $this->assertStringContainsString('name="foo[]"', $matches[3], var_export($matches, 1));
            $this->assertStringContainsString('value="' . $key . '"', $matches[3], var_export($matches, 1));
        }
    }

    public function testRendersAsHtmlByDefault()
    {
        $options = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
        $html = $this->helper->formMultiCheckbox([
            'name' => 'foo',
            'value' => 'bar',
            'options' => $options,
        ]);
        foreach ($options as $key => $value) {
            $pattern = '#(<input[^>]*?("' . $key . '").*?>)#';
            if (!preg_match($pattern, $html, $matches)) {
                $this->fail('Failed to match ' . $pattern . ': ' . $html);
            }
            $this->assertStringNotContainsString(' />', $matches[1]);
        }
    }
}
