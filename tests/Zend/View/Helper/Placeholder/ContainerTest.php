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

/** Zend_View_Helper_Placeholder_Container */
require_once 'Zend/View/Helper/Placeholder/Container.php';

/**
 * Test class for Zend_View_Helper_Placeholder_Container.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
#[AllowDynamicProperties]
class Zend_View_Helper_Placeholder_ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_View_Helper_Placeholder_Container
     */
    public $container;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_View_Helper_Placeholder_ContainerTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->container = new Zend_View_Helper_Placeholder_Container([]);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        unset($this->container);
    }

    public function testSetSetsASingleValue()
    {
        $this->container['foo'] = 'bar';
        $this->container['bar'] = 'baz';
        $this->assertEquals('bar', $this->container['foo']);
        $this->assertEquals('baz', $this->container['bar']);

        $this->container->set('foo');
        $this->assertEquals(1, count($this->container));
        $this->assertEquals('foo', $this->container[0]);
    }

    public function testGetValueReturnsScalarWhenOneElementRegistered()
    {
        $this->container->set('foo');
        $this->assertEquals('foo', $this->container->getValue());
    }

    public function testGetValueReturnsArrayWhenMultipleValuesPresent()
    {
        $this->container['foo'] = 'bar';
        $this->container['bar'] = 'baz';
        $expected = ['foo' => 'bar', 'bar' => 'baz'];
        $return = $this->container->getValue();
        $this->assertEquals($expected, $return);
    }

    public function testPrefixAccesorsWork()
    {
        $this->assertEquals('', $this->container->getPrefix());
        $this->container->setPrefix('<ul><li>');
        $this->assertEquals('<ul><li>', $this->container->getPrefix());
    }

    public function testSetPrefixImplementsFluentInterface()
    {
        $result = $this->container->setPrefix('<ul><li>');
        $this->assertSame($this->container, $result);
    }

    public function testPostfixAccesorsWork()
    {
        $this->assertEquals('', $this->container->getPostfix());
        $this->container->setPostfix('</li></ul>');
        $this->assertEquals('</li></ul>', $this->container->getPostfix());
    }

    public function testSetPostfixImplementsFluentInterface()
    {
        $result = $this->container->setPostfix('</li></ul>');
        $this->assertSame($this->container, $result);
    }

    public function testSeparatorAccesorsWork()
    {
        $this->assertEquals('', $this->container->getSeparator());
        $this->container->setSeparator('</li><li>');
        $this->assertEquals('</li><li>', $this->container->getSeparator());
    }

    public function testSetSeparatorImplementsFluentInterface()
    {
        $result = $this->container->setSeparator('</li><li>');
        $this->assertSame($this->container, $result);
    }

    public function testIndentAccesorsWork()
    {
        $this->assertEquals('', $this->container->getIndent());
        $this->container->setIndent('    ');
        $this->assertEquals('    ', $this->container->getIndent());
        $this->container->setIndent(5);
        $this->assertEquals('     ', $this->container->getIndent());
    }

    public function testSetIndentImplementsFluentInterface()
    {
        $result = $this->container->setIndent('    ');
        $this->assertSame($this->container, $result);
    }

    public function testCapturingToPlaceholderStoresContent()
    {
        $this->container->captureStart();
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $value = $this->container->getValue();
        $this->assertStringContainsString('This is content intended for capture', $value);
    }

    public function testCapturingToPlaceholderAppendsContent()
    {
        $this->container[] = 'foo';
        $originalCount = count($this->container);

        $this->container->captureStart();
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->container));

        $value = $this->container->getValue();
        $keys = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex - 1]);
        $this->assertStringContainsString('This is content intended for capture', $value[$lastIndex]);
    }

    public function testCapturingToPlaceholderUsingPrependPrependsContent()
    {
        $this->container[] = 'foo';
        $originalCount = count($this->container);

        $this->container->captureStart('PREPEND');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->container));

        $value = $this->container->getValue();
        $keys = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex]);
        $this->assertStringContainsString('This is content intended for capture', $value[$lastIndex - 1]);
    }

    public function testCapturingToPlaceholderUsingSetOverwritesContent()
    {
        $this->container[] = 'foo';
        $this->container->captureStart('SET');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));

        $value = $this->container->getValue();
        $this->assertStringContainsString('This is content intended for capture', $value);
    }

    public function testCapturingToPlaceholderKeyUsingSetCapturesContent()
    {
        $this->container->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertStringContainsString('This is content intended for capture', $value);
    }

    public function testCapturingToPlaceholderKeyUsingSetReplacesContentAtKey()
    {
        $this->container['key'] = 'Foobar';
        $this->container->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertStringContainsString('This is content intended for capture', $value);
    }

    public function testCapturingToPlaceholderKeyUsingAppendAppendsContentAtKey()
    {
        $this->container['key'] = 'Foobar ';
        $this->container->captureStart('APPEND', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertStringContainsString('Foobar This is content intended for capture', $value);
    }

    public function testNestedCapturesThrowsException()
    {
        $this->container[] = 'foo';
        $caught = false;

        try {
            $this->container->captureStart('SET');
            $this->container->captureStart('SET');
            $this->container->captureEnd();
            $this->container->captureEnd();
        } catch (Exception $e) {
            $this->container->captureEnd();
            $caught = true;
        }

        $this->assertTrue($caught, 'Nested captures should throw exceptions');
    }

    public function testToStringWithNoModifiersAndSingleValueReturnsValue()
    {
        $this->container->set('foo');
        $value = $this->container->toString();
        $this->assertEquals($this->container->getValue(), $value);
    }

    public function testToStringWithModifiersAndSingleValueReturnsFormattedValue()
    {
        $this->container->set('foo');
        $this->container->setPrefix('<li>')
            ->setPostfix('</li>');
        $value = $this->container->toString();
        $this->assertEquals('<li>foo</li>', $value);
    }

    public function testToStringWithNoModifiersAndCollectionReturnsImplodedString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $value = $this->container->toString();
        $this->assertEquals('foobarbaz', $value);
    }

    public function testToStringWithModifiersAndCollectionReturnsFormattedString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
            ->setSeparator('</li><li>')
            ->setPostfix('</li></ul>');
        $value = $this->container->toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    public function testToStringWithModifiersAndCollectionReturnsFormattedStringWithIndentation()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
            ->setSeparator('</li>' . PHP_EOL . '<li>')
            ->setPostfix('</li></ul>')
            ->setIndent('    ');
        $value = $this->container->toString();
        $expectedValue = '    <ul><li>foo</li>' . PHP_EOL . '    <li>bar</li>' . PHP_EOL . '    <li>baz</li></ul>';
        $this->assertEquals($expectedValue, $value);
    }

    public function test__toStringProxiesToToString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
            ->setSeparator('</li><li>')
            ->setPostfix('</li></ul>');
        $value = $this->container->__toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    public function testPrependPushesValueToTopOfContainer()
    {
        $this->container['foo'] = 'bar';
        $this->container->prepend('baz');

        $expected = ['baz', 'foo' => 'bar'];
        $array = $this->container->getArrayCopy();
        $this->assertSame($expected, $array);
    }

    public function testIndentationIsHonored()
    {
        $this->container->setIndent(4)
            ->setPrefix("<ul>\n    <li>")
            ->setSeparator("</li>\n    <li>")
            ->setPostfix("</li>\n</ul>");
        $this->container->append('foo');
        $this->container->append('bar');
        $this->container->append('baz');
        $string = $this->container->toString();

        $lis = substr_count($string, "\n        <li>");
        $this->assertEquals(3, $lis);
        $this->assertTrue((strstr($string, "    <ul>\n")) ? true : false, $string);
        $this->assertTrue((strstr($string, "\n    </ul>")) ? true : false);
    }

    /**
     * @group ZF-12044
     */
    public function testContainerWithoutItemsShouldAlwaysReturnEmptyString()
    {
        $this->assertEquals('', (string) $this->container);

        $this->container->setIndent(4);
        $this->assertEquals('', (string) $this->container);

        $this->container->setPrefix('<ul><li>');
        $this->assertEquals('', (string) $this->container);

        $this->container->setSeparator('</li><li>');
        $this->assertEquals('', (string) $this->container);

        $this->container->setPrefix('</li></ul>');
        $this->assertEquals('', (string) $this->container);
    }
}
