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
require_once 'Zend/Form.php';

/**
 * Test class for Zend_Form_Decorator_Form.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_FormTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_FormTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_Form();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');

        return $view;
    }

    public function testHelperIsFormByDefault()
    {
        $this->assertEquals('form', $this->decorator->getHelper());
    }

    public function testCanSetHelperWithOption()
    {
        $this->testHelperIsFormByDefault();
        $this->decorator->setOption('helper', 'formForm');
        $this->assertEquals('formForm', $this->decorator->getHelper());

        $attribs = [
            'enctype' => 'ascii',
            'charset' => 'us-ascii',
        ];
        $loader = new Zend_Loader_PluginLoader(['Zend_Form_Decorator' => 'Zend/Form/Decorator/']);
        $displayGroup = new Zend_Form_DisplayGroup('foo', $loader, ['attribs' => $attribs]);
        $this->decorator->setElement($displayGroup);
        $options = $this->decorator->getOptions();
        $this->assertTrue(isset($options['enctype']));
        $this->assertEquals($attribs['enctype'], $options['enctype']);
        $this->assertTrue(isset($options['charset']));
        $this->assertEquals($attribs['charset'], $options['charset']);
    }

    /**
     * @group ZF-3643
     */
    public function testShouldPreferFormIdAttributeOverFormName()
    {
        $form = new Zend_Form();
        $form->setMethod('post')
            ->setAction('/foo/bar')
            ->setName('foobar')
            ->setAttrib('id', 'bazbat')
            ->setView($this->getView());
        $html = $form->render();
        $this->assertStringContainsString('id="bazbat"', $html, $html);
    }

    public function testEmptyFormNameShouldNotRenderEmptyFormId()
    {
        $form = new Zend_Form();
        $form->setMethod('post')
            ->setAction('/foo/bar')
            ->setView($this->getView());
        $html = $form->render();
        $this->assertStringNotContainsString('id=""', $html, $html);
    }
}
