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

/** Zend_View_Helper_Doctype */
require_once 'Zend/View/Helper/Doctype.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_Doctype.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_DoctypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_View_Helper_Doctype
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_DoctypeTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $regKey = \Zend_View_Helper_Doctype::class;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->helper = new Zend_View_Helper_Doctype();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testRegistryEntryCreatedAfterInstantiation()
    {
        $this->assertTrue(Zend_Registry::isRegistered(\Zend_View_Helper_Doctype::class));
        $doctype = Zend_Registry::get(\Zend_View_Helper_Doctype::class);
        $this->assertTrue($doctype instanceof ArrayObject);
        $this->assertTrue(isset($doctype['doctype']));
        $this->assertTrue(isset($doctype['doctypes']));
        $this->assertTrue(is_array($doctype['doctypes']));
    }

    public function testDoctypeMethodReturnsObjectInstance()
    {
        $doctype = $this->helper->doctype();
        $this->assertTrue($doctype instanceof Zend_View_Helper_Doctype);
    }

    public function testIsXhtmlReturnsFalseForNonXhtmlDoctypes()
    {
        foreach (['HTML5'] as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertFalse($doctype->isXhtml());
        }
    }

    public function testIsHtml5()
    {
        foreach (['HTML5'] as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertTrue($doctype->isHtml5());
        }
    }

    public function testIsRdfa()
    {
        // built-in doctypes
        foreach (['HTML5'] as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertFalse($doctype->isRdfa());
        }
    }

    public function testStringificationReturnsDoctypeString()
    {
        $doctype = $this->helper->doctype('HTML5');
        $string = $doctype->__toString();
        $registry = Zend_Registry::get(\Zend_View_Helper_Doctype::class);
        $this->assertEquals($registry['doctypes']['HTML5'], $string);
    }
}
