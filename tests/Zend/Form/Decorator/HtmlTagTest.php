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
require_once 'Zend/Form/Decorator/HtmlTag.php';

require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_HtmlTag.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Form
 */
#[AllowDynamicProperties]
class Zend_Form_Decorator_HtmlTagTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite('Zend_Form_Decorator_HtmlTagTest');
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        $this->decorator = new Zend_Form_Decorator_HtmlTag();
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

    public function testNormalizeTagStripsNonAlphanumericCharactersAndLowersCase()
    {
        $tag = 'ab1-cd0EFG';
        $received = $this->decorator->normalizeTag($tag);
        $this->assertEquals('ab1cd0efg', $received);
    }

    public function testRendersOptionsAsHtmlAttribsByDefault()
    {
        $element = new Zend_Form_Element('foo');
        $options = ['tag' => 'div', 'class' => 'foobar', 'id' => 'foo'];
        $this->decorator->setElement($element)
            ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertStringContainsString('<' . $value, $html);
                $this->assertStringContainsString('</' . $value . '>', $html);
            } else {
                $this->assertStringContainsString($key . '="' . $value . '"', $html);
            }
        }
    }

    public function testDoesNotRenderAttribsWhenNoAttribsOptionSet()
    {
        $element = new Zend_Form_Element('foo');
        $options = ['tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'noAttribs' => true];
        $this->decorator->setElement($element)
            ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertStringContainsString('<' . $value, $html);
                $this->assertStringContainsString('</' . $value . '>', $html);
            } else {
                $this->assertStringNotContainsString($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyOpeningTag()
    {
        $element = new Zend_Form_Element('foo');
        $options = ['tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'openOnly' => true];
        $this->decorator->setElement($element)
            ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertStringContainsString('<' . $value, $html);
                $this->assertStringNotContainsString('</' . $value . '>', $html);
            } elseif ('openOnly' == $key) {
                $this->assertStringNotContainsString($key, $html);
            } else {
                $this->assertStringContainsString($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyClosingTag()
    {
        $element = new Zend_Form_Element('foo');
        $options = ['tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'closeOnly' => true];
        $this->decorator->setElement($element)
            ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertStringNotContainsString('<' . $value, $html);
                $this->assertStringContainsString('</' . $value . '>', $html);
            } else {
                $this->assertStringNotContainsString($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testArrayAttributesAreRenderedAsSpaceSeparatedLists()
    {
        $element = new Zend_Form_Element('foo');
        $options = ['tag' => 'div', 'class' => ['foobar', 'bazbat'], 'id' => 'foo'];
        $this->decorator->setElement($element)
            ->setOptions($options);
        $html = $this->decorator->render('');
        $this->assertStringContainsString('class="foobar bazbat"', $html);
    }

    public function testAppendPlacementWithCloseOnlyRendersClosingTagFollowingContent()
    {
        $options = [
            'closeOnly' => true,
            'tag' => 'div',
            'placement' => 'append',
        ];
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertMatchesRegularExpression('#(content).*?(</div>)#', $html, $html);
    }

    public function testAppendPlacementWithOpenOnlyRendersOpeningTagFollowingContent()
    {
        $options = [
            'openOnly' => true,
            'tag' => 'div',
            'placement' => 'append',
        ];
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertMatchesRegularExpression('#(content).*?(<div>)#', $html, $html);
    }

    public function testPrependPlacementWithCloseOnlyRendersClosingTagBeforeContent()
    {
        $options = [
            'closeOnly' => true,
            'tag' => 'div',
            'placement' => 'prepend',
        ];
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertMatchesRegularExpression('#(</div>).*?(content)#', $html, $html);
    }

    public function testPrependPlacementWithOpenOnlyRendersOpeningTagBeforeContent()
    {
        $options = [
            'openOnly' => true,
            'tag' => 'div',
            'placement' => 'prepend',
        ];
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertMatchesRegularExpression('#(<div>).*?(content)#', $html, $html);
    }

    public function testTagIsInitiallyDiv()
    {
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testCanSetTag()
    {
        $this->testTagIsInitiallyDiv();
        $this->decorator->setTag('dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }

    public function testCanSetTagViaOption()
    {
        $this->decorator->setOption('tag', 'dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }
}
