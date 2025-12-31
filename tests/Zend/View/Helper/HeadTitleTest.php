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

/** Zend_View_Helper_HeadTitle */
require_once 'Zend/View/Helper/HeadTitle.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HeadTitle.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
#[AllowDynamicProperties]
#[PHPUnit\Framework\Attributes\Group('Zend_View')]
#[PHPUnit\Framework\Attributes\Group('Zend_View_Helper')]
class Zend_View_Helper_HeadTitleTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_View_Helper_HeadTitle
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadTitle();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists(Zend_View_Helper_HeadTitle::class)) {
            $registry->deleteContainer(Zend_View_Helper_HeadTitle::class);
        }
        $this->assertFalse($registry->containerExists(Zend_View_Helper_HeadTitle::class));
        $helper = new Zend_View_Helper_HeadTitle();
        $this->assertTrue($registry->containerExists(Zend_View_Helper_HeadTitle::class));
    }

    public function testHeadTitleReturnsObjectInstance()
    {
        $placeholder = $this->helper->headTitle();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadTitle);
    }

    public function testCanSetTitleViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo Bar', 'SET');
        $this->assertStringContainsString('Foo Bar', $placeholder->toString());
    }

    public function testCanAppendTitleViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar');
        $this->assertStringContainsString('FooBar', $placeholder->toString());
    }

    public function testCanPrependTitleViaHeadTitle()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar', 'PREPEND');
        $this->assertStringContainsString('BarFoo', $placeholder->toString());
    }

    public function testReturnedPlaceholderToStringContainsFullTitleElement()
    {
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar', 'APPEND')->setSeparator(' :: ');
        $this->assertEquals('<title>Foo :: Bar</title>', $placeholder->toString());
    }

    public function testToStringEscapesEntries()
    {
        $this->helper->headTitle('<script type="text/javascript">alert("foo");</script>');
        $string = $this->helper->toString();
        $this->assertStringNotContainsString('<script', $string);
        $this->assertStringNotContainsString('</script>', $string);
    }

    public function testToStringEscapesSeparator()
    {
        $this->helper->headTitle('Foo')
            ->headTitle('Bar')
            ->setSeparator(' <br /> ');
        $string = $this->helper->toString();
        $this->assertStringNotContainsString('<br />', $string);
        $this->assertStringContainsString('Foo', $string);
        $this->assertStringContainsString('Bar', $string);
        $this->assertStringContainsString('br /', $string);
    }

    public function testIndentationIsHonored()
    {
        $this->helper->setIndent(4);
        $this->helper->headTitle('foo');
        $string = $this->helper->toString();

        $this->assertStringContainsString('    <title>', $string);
    }

    public function testAutoEscapeIsHonored()
    {
        $this->helper->headTitle('Some Title &copyright;');
        $this->assertEquals('<title>Some Title &amp;copyright;</title>', $this->helper->toString());

        $this->assertTrue($this->helper->headTitle()->getAutoEscape());
        $this->helper->headTitle()->setAutoEscape(false);
        $this->assertFalse($this->helper->headTitle()->getAutoEscape());

        $this->assertEquals('<title>Some Title &copyright;</title>', $this->helper->toString());
    }

    /**
     * @see http://framework.zend.com/issues/browse/ZF-2918
     */
    #[PHPUnit\Framework\Attributes\Group('ZF-2918')]
    public function testZF2918()
    {
        $this->helper->headTitle('Some Title');
        $this->helper->setPrefix('Prefix: ');
        $this->helper->setPostfix(' :Postfix');

        $this->assertEquals('<title>Prefix: Some Title :Postfix</title>', $this->helper->toString());
    }

    /**
     * @see http://framework.zend.com/issues/browse/ZF-3577
     */
    #[PHPUnit\Framework\Attributes\Group('ZF-3577')]
    public function testZF3577()
    {
        $this->helper->setAutoEscape(true);
        $this->helper->headTitle('Some Title');
        $this->helper->setPrefix('Prefix & ');
        $this->helper->setPostfix(' & Postfix');

        $this->assertEquals('<title>Prefix &amp; Some Title &amp; Postfix</title>', $this->helper->toString());
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-8036')]
    public function testHeadTitleZero()
    {
        $this->helper->headTitle('0');
        $this->assertEquals('<title>0</title>', $this->helper->toString());
    }

    public function testCanPrependTitlesUsingDefaultAttachOrder()
    {
        $this->helper->setDefaultAttachOrder('PREPEND');
        $placeholder = $this->helper->headTitle('Foo');
        $placeholder = $this->helper->headTitle('Bar');
        $this->assertStringContainsString('BarFoo', $placeholder->toString());
    }

    #[PHPUnit\Framework\Attributes\Group('ZF-10284')]
    public function testReturnTypeDefaultAttachOrder()
    {
        $this->assertTrue($this->helper->setDefaultAttachOrder('PREPEND') instanceof Zend_View_Helper_HeadTitle);
        $this->assertEquals('PREPEND', $this->helper->getDefaultAttachOrder());
    }
}
